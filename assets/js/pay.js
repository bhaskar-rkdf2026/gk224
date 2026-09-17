/* ==========================================================================
   GK224.COM - Payments, GK SecurePay & Transactions Engine (pay.js)
   ========================================================================== */

let payType = 'send';
const formTitles = { send: 'Send Money', request: 'Request Money', bill: 'Pay Bills' };

function openPayForm(type) {
  payType = type;
  const titleEl = document.getElementById('payFormTitle');
  if (titleEl) titleEl.textContent = formTitles[type] || 'Payment';
  const formEl = document.getElementById('payForm');
  if (formEl) formEl.style.display = 'block';
  const amtEl = document.getElementById('payAmount');
  if (amtEl) amtEl.focus();
}

function closePayForm() {
  const formEl = document.getElementById('payForm');
  if (formEl) formEl.style.display = 'none';
  ['payAmount', 'payDesc', 'payCat'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.value = '';
  });
}

function submitPayment() {
  const amtEl = document.getElementById('payAmount');
  const descEl = document.getElementById('payDesc');
  const catEl = document.getElementById('payCat');

  const amount = parseFloat(amtEl.value);
  const desc = descEl.value.trim();
  const cat = catEl.value;

  if (!amount || amount <= 0) { showToast('Enter a valid amount', 'error'); return; }
  if (!desc) { showToast('Add a description', 'error'); return; }
  if (!cat) { showToast('Select a category', 'error'); return; }

  const currentBal = GKStore.getBalance();
  if (amount > currentBal) { showToast('Insufficient wallet balance', 'error'); return; }

  const btn = document.getElementById('paySubmitBtn');
  if (btn) {
    btn.disabled = true;
    btn.textContent = 'Processing...';
  }

  setTimeout(() => {
    GKStore.addTransaction({
      type: 'payment',
      title: desc,
      amount: -amount,
      category: cat,
      ico: catIco(cat)
    });

    autoCashback(amount, cat, desc);
    showToast('Payment processed successfully! 🎉', 'success');
    closePayForm();

    if (btn) {
      btn.disabled = false;
      btn.textContent = 'Process Payment';
    }
    renderPayTransactions();
  }, 700);
}

// ─── AUTO CASHBACK ENGINE ─────────────────────────────────
const CASHBACK_RATES = {
  food: 0.02, transport: 0.03, shopping: 0.05, bills: 0.01,
  entertainment: 0.04, travel: 0.06, other: 0.01
};

function autoCashback(amount, category, sourceTitle) {
  const pct = CASHBACK_RATES[category] ?? CASHBACK_RATES.other;
  const multiplier = GKStore.isProUser() ? 2 : 1;
  const cashback = Math.max(1, Math.round(amount * pct * multiplier));

  GKStore.addTransaction({
    type: 'earning',
    title: 'Cashback: ' + sourceTitle,
    amount: cashback,
    ico: catIco(category)
  });

  showToast(`+₹${cashback} cashback credited! 💰`, 'success');
  addNotification('Cashback earned 💰', `You got ₹${cashback} cashback (${Math.round(pct * 100 * multiplier)}%) on "${sourceTitle}".`, 'ri-coins-line');
}

function catIco(cat) {
  const m = {
    food: '<i class="ri-restaurant-2-line"></i>',
    transport: '<i class="ri-car-line"></i>',
    shopping: '<i class="ri-shopping-bag-3-line"></i>',
    bills: '<i class="ri-flashlight-line"></i>',
    entertainment: '<i class="ri-gamepad-line"></i>',
    travel: '<i class="ri-flight-takeoff-line"></i>',
    other: '<i class="ri-more-2-fill"></i>'
  };
  return m[cat] || '<i class="ri-more-2-fill"></i>';
}

function renderPayTransactions() {
  const payEl = document.getElementById('payTransactions');
  if (!payEl) return;
  const txs = GKStore.getTransactions().filter(t => ['payment','travel','learning'].includes(t.type));
  if (!txs.length) {
    payEl.innerHTML = '<div class="empty"><div class="empty-ico"><i class="ri-bank-card-2-line"></i></div><p>No transactions yet</p></div>';
    return;
  }
  payEl.innerHTML = txs.slice(0, 10).map(t => {
    const d = new Date(t.date);
    const dateStr = `${d.getDate()} ${d.toLocaleString('en-IN', { month: 'short' })}`;
    return `
      <div class="tx-item">
        <div class="tx-left">
          <div class="tx-ico">${t.ico || '<i class="ri-bank-card-line"></i>'}</div>
          <div>
            <div class="tx-title">${t.title}</div>
            <div class="tx-date">${dateStr}</div>
          </div>
        </div>
        <div class="tx-amount neg">-₹${Math.abs(t.amount).toLocaleString('en-IN')}</div>
      </div>
    `;
  }).join('');
}

// ─── GK SECUREPAY ADD MONEY ───────────────────────────────
let amAmount = 0;
let amMethod = '';
let amPurpose = 'topup';
let amProPlan = null;
const amSteps = ['amStepAmount','amStepMethods','amStepUPI','amStepCard','amStepNetbanking','amStepProcessing','amStepSuccess'];

function amShowStep(id) {
  amSteps.forEach(s => {
    const el = document.getElementById(s);
    if (el) el.classList.remove('active');
  });
  const target = document.getElementById(id);
  if (target) target.classList.add('active');
}

function openAddMoney() {
  amAmount = 0;
  amMethod = '';
  amPurpose = 'topup';
  amProPlan = null;
  const input = document.getElementById('amAmountInput');
  if (input) input.value = '';
  document.querySelectorAll('.am-chip').forEach(c => c.classList.remove('sel'));
  amShowStep('amStepAmount');
  const modal = document.getElementById('addMoneyModal');
  if (modal) modal.classList.add('open');
}

function openAddMoneyForPro(planKey, price) {
  amAmount = price;
  amMethod = '';
  amPurpose = 'pro';
  amProPlan = { key: planKey, price: price };
  const methodsAmt = document.getElementById('amMethodsAmt');
  if (methodsAmt) methodsAmt.textContent = '₹' + price.toLocaleString('en-IN');
  amShowStep('amStepMethods');
  const modal = document.getElementById('addMoneyModal');
  if (modal) modal.classList.add('open');
}

function closeAddMoney() {
  const modal = document.getElementById('addMoneyModal');
  if (modal) modal.classList.remove('open');
}

function closeModalOutsideAddMoney(e) {
  if (e.target === document.getElementById('addMoneyModal')) closeAddMoney();
}

function amPickChip(val, el) {
  document.querySelectorAll('.am-chip').forEach(c => c.classList.remove('sel'));
  el.classList.add('sel');
  const input = document.getElementById('amAmountInput');
  if (input) input.value = val;
}

function amClearChipSel() {
  document.querySelectorAll('.am-chip').forEach(c => c.classList.remove('sel'));
}

function amGoToMethods() {
  const input = document.getElementById('amAmountInput');
  const val = parseFloat(input.value);
  if (!val || val <= 0) { showToast('Enter a valid amount', 'error'); return; }
  amAmount = Math.round(val);
  const methodsAmt = document.getElementById('amMethodsAmt');
  if (methodsAmt) methodsAmt.textContent = '₹' + amAmount.toLocaleString('en-IN');
  amShowStep('amStepMethods');
}

function amBackToAmount() { amShowStep('amStepAmount'); }
function amBackToMethods() { amShowStep('amStepMethods'); }

function amSelectMethod(method) {
  amMethod = method;
  const amtStr = '₹' + amAmount.toLocaleString('en-IN');
  if (method === 'upi') {
    document.getElementById('amUpiAmt').textContent = amtStr;
    amShowStep('amStepUPI');
  } else if (method === 'card') {
    document.getElementById('amCardAmt').textContent = amtStr;
    amShowStep('amStepCard');
  } else if (method === 'netbanking') {
    document.getElementById('amNbAmt').textContent = amtStr;
    amShowStep('amStepNetbanking');
  }
}

function amProcess(method) {
  if (method === 'upi') {
    const upi = document.getElementById('amUpiId').value.trim();
    if (!upi || !upi.includes('@')) { showToast('Enter a valid UPI ID (e.g. name@upi)', 'error'); return; }
  } else if (method === 'card') {
    const num = document.getElementById('amCardNum').value.replace(/\s/g, '');
    const name = document.getElementById('amCardName').value.trim();
    if (num.length < 16 || !name) { showToast('Fill valid card details', 'error'); return; }
  } else if (method === 'netbanking') {
    const bank = document.getElementById('amNbBank').value;
    if (!bank) { showToast('Select your bank', 'error'); return; }
  }

  amShowStep('amStepProcessing');

  setTimeout(() => {
    const methodLabels = { upi: 'UPI', card: 'Card', netbanking: 'Net Banking' };
    const txnId = 'GKSP' + Date.now().toString().slice(-10);

    if (amPurpose === 'pro') {
      GKStore.setProUser(amProPlan.key);
      showToast('Welcome to GK Pro! 👑', 'success');
      addNotification('GK Pro activated', 'Your GK Pro subscription is active — enjoy 2x cashback!', 'ri-vip-crown-2-fill');
    } else {
      GKStore.addTransaction({
        type: 'earning',
        title: 'Added Money via GK SecurePay (' + methodLabels[method] + ')',
        amount: amAmount,
        ico: '<i class="ri-wallet-3-line"></i>'
      });
      showToast('₹' + amAmount.toLocaleString('en-IN') + ' added to wallet! 🎉', 'success');
    }

    document.getElementById('amSuccessAmt').textContent = '₹' + amAmount.toLocaleString('en-IN');
    document.getElementById('amTxnId').textContent = txnId;
    document.getElementById('amTxnMethod').textContent = methodLabels[method];
    document.getElementById('amTxnDate').textContent = new Date().toLocaleString('en-IN', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' });

    amShowStep('amStepSuccess');
  }, 1400);
}

document.addEventListener('DOMContentLoaded', function() {
  renderPayTransactions();
  window.addEventListener('gkStateChanged', renderPayTransactions);
});
