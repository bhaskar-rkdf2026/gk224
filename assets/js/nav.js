/* ==========================================================================
   GK224.COM - Navigation, Header, Notifications, Modals & Pro Engine (nav.js)
   ========================================================================== */

let toastTimer;
function showToast(msg, type = 'success') {
  let el = document.getElementById('toast');
  if (!el) {
    el = document.createElement('div');
    el.id = 'toast';
    el.className = 'toast';
    document.body.appendChild(el);
  }
  el.textContent = msg;
  el.className = `toast ${type} show`;
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => el.classList.remove('show'), 2800);
}

// ─── BALANCE ANIMATION ────────────────────────────────────
let displayedBalance = 0;
let balanceAnimFrame = null;

function animateBalanceTo(target, duration = 800) {
  const el = document.getElementById('totalBalance');
  if (!el) return;
  const start = displayedBalance || target;
  const diff = target - start;
  const startTime = performance.now();

  if (balanceAnimFrame) cancelAnimationFrame(balanceAnimFrame);

  function step(now) {
    const t = Math.min(1, (now - startTime) / duration);
    const eased = 1 - Math.pow(1 - t, 3);
    const current = Math.round(start + diff * eased);
    el.innerHTML = `<span>₹</span>${current.toLocaleString('en-IN')}`;
    if (t < 1) {
      balanceAnimFrame = requestAnimationFrame(step);
    } else {
      displayedBalance = target;
      el.innerHTML = `<span>₹</span>${target.toLocaleString('en-IN')}`;
      el.classList.add('pulse');
      setTimeout(() => el.classList.remove('pulse'), 500);
    }
  }
  balanceAnimFrame = requestAnimationFrame(step);
}

function updateHeaderBalance() {
  const bal = GKStore.getBalance();
  animateBalanceTo(bal);
  const txCount = document.getElementById('txCount');
  if (txCount) txCount.textContent = GKStore.getTransactions().length;
}

// ─── NOTIFICATIONS ENGINE ─────────────────────────────────
let notifAutomationOn = false;
let notifTimers = [];

function addNotification(title, text, icon) {
  const list = GKStore.getNotifications();
  const notif = {
    id: Date.now() + Math.random(),
    title,
    text,
    icon: icon || 'ri-notification-3-line',
    time: Date.now(),
    read: false
  };
  list.unshift(notif);
  GKStore.saveNotifications(list);
  renderNotifications();
  updateBellBadge();
  ringBell();

  if (notifAutomationOn && 'Notification' in window && Notification.permission === 'granted') {
    try { new Notification(title, { body: text }); } catch (e) {}
  }
}

function renderNotifications() {
  const listEl = document.getElementById('notifList');
  const emptyEl = document.getElementById('notifEmpty');
  if (!listEl) return;
  const list = GKStore.getNotifications();
  if (!list.length) {
    listEl.innerHTML = '';
    if (emptyEl) emptyEl.style.display = 'block';
    return;
  }
  if (emptyEl) emptyEl.style.display = 'none';
  listEl.innerHTML = list.map(n => `
    <div class="notif-item ${n.read ? '' : 'unread'}">
      <div class="notif-ico"><i class="${n.icon}"></i></div>
      <div class="notif-body">
        <div class="notif-title">${n.title}</div>
        <div class="notif-text">${n.text}</div>
        <div class="notif-time">${timeAgo(n.time)}</div>
      </div>
    </div>
  `).join('');
}

function timeAgo(ts) {
  const s = Math.floor((Date.now() - ts) / 1000);
  if (s < 60) return 'Just now';
  const m = Math.floor(s / 60);
  if (m < 60) return `${m} min ago`;
  const h = Math.floor(m / 60);
  if (h < 24) return `${h} hr ago`;
  return `${Math.floor(h / 24)} day ago`;
}

function updateBellBadge() {
  const badge = document.getElementById('bellBadge');
  if (!badge) return;
  const unread = GKStore.getNotifications().filter(n => !n.read).length;
  if (unread > 0) {
    badge.style.display = 'flex';
    badge.textContent = unread > 9 ? '9+' : unread;
  } else {
    badge.style.display = 'none';
  }
}

function ringBell() {
  const bell = document.getElementById('bellBtn');
  if (!bell) return;
  bell.classList.remove('ringing');
  void bell.offsetWidth;
  bell.classList.add('ringing');
}

function openNotifications() {
  const list = GKStore.getNotifications();
  list.forEach(n => n.read = true);
  GKStore.saveNotifications(list);
  renderNotifications();
  updateBellBadge();
  const modal = document.getElementById('notifModal');
  if (modal) modal.classList.add('open');
}

function closeNotifications() {
  const modal = document.getElementById('notifModal');
  if (modal) modal.classList.remove('open');
}

function closeModalOutsideNotif(e) {
  if (e.target === document.getElementById('notifModal')) closeNotifications();
}

function toggleNotifAutomation(checked) {
  notifAutomationOn = checked;
  const statusEl = document.getElementById('notifPermStatus');
  if (checked && 'Notification' in window) {
    Notification.requestPermission().then(perm => {
      if (perm === 'granted') {
        if (statusEl) statusEl.textContent = 'Push alerts enabled ✓';
      }
    });
  }
}

// ─── GK PRO UPGRADE ENGINE ────────────────────────────────
function applyProUI() {
  const isPro = GKStore.isProUser();
  const badge = document.getElementById('proBadge');
  const banner = document.getElementById('proBanner');
  if (badge) badge.style.display = isPro ? 'inline-flex' : 'none';
  if (banner) banner.style.display = isPro ? 'none' : 'flex';
}

function openProModal() {
  const modal = document.getElementById('proModal');
  if (modal) modal.classList.add('open');
}

function closeProModal() {
  const modal = document.getElementById('proModal');
  if (modal) modal.classList.remove('open');
}

function closeModalOutsidePro(e) {
  if (e.target === document.getElementById('proModal')) closeProModal();
}

function selectProPlan(key, price) {
  closeProModal();
  if (typeof openAddMoneyForPro === 'function') {
    openAddMoneyForPro(key, price);
  } else {
    GKStore.setProUser(key);
    applyProUI();
    showToast('GK Pro Activated! 👑 Enjoy 2x cashback.', 'success');
  }
}

// ─── CONFIRM ACTION MODAL ─────────────────────────────────
let pendingConfirmAction = null;

function openConfirmModal(opts) {
  const modal = document.getElementById('confirmActionModal');
  if (!modal) {
    if (opts.onConfirm) opts.onConfirm();
    return;
  }
  document.getElementById('confirmActionIcon').className = opts.icon || 'ri-checkbox-circle-line';
  document.getElementById('confirmActionModalTitle').textContent = opts.title || 'Confirm';
  document.getElementById('confirmActionModalSub').textContent = opts.sub || 'Please review before continuing';
  document.getElementById('confirmActionCardIcon').innerHTML = opts.itemIcon || '<i class="ri-checkbox-circle-line"></i>';
  document.getElementById('confirmActionCardTitle').textContent = opts.itemTitle || '';
  document.getElementById('confirmActionCardDesc').textContent = opts.itemDesc || '';
  document.getElementById('confirmActionPriceLabel').textContent = opts.priceLabel || 'Amount';
  document.getElementById('confirmActionPrice').textContent = opts.price || '₹0';
  document.getElementById('confirmActionBtn').textContent = opts.confirmLabel || 'Confirm';

  pendingConfirmAction = opts.onConfirm;
  modal.classList.add('open');
}

function closeConfirmAction() {
  const modal = document.getElementById('confirmActionModal');
  if (modal) modal.classList.remove('open');
  pendingConfirmAction = null;
}

function runConfirmAction() {
  if (typeof pendingConfirmAction === 'function') {
    pendingConfirmAction();
  }
  closeConfirmAction();
}

function closeModalOutsideConfirmAction(e) {
  if (e.target === document.getElementById('confirmActionModal')) closeConfirmAction();
}

// ─── WELCOME CONFETTI ─────────────────────────────────────
function spawnConfetti() {
  const wrap = document.getElementById('wpopConfetti');
  if (!wrap) return;
  wrap.innerHTML = '';
  const colors = ['#b5d400', '#a8a8b0', '#6e8c00', '#f0f0f5'];
  for (let c = 0; c < 40; c++) {
    const piece = document.createElement('i');
    piece.style.left = Math.random() * 100 + '%';
    piece.style.background = colors[c % colors.length];
    piece.style.animationDuration = (2.2 + Math.random() * 1.6) + 's';
    piece.style.animationDelay = (Math.random() * 0.6) + 's';
    wrap.appendChild(piece);
  }
}

function closeWelcomePopup() {
  const pop = document.getElementById('welcomePopup');
  if (pop) pop.classList.remove('show');
}

// ─── ICON CLICK RIPPLE ────────────────────────────────────
document.addEventListener('click', function(e) {
  const icon = e.target.closest('.ico, .nav-icon, .notif-ico, .pro-banner-ico, .ref-ico, .tx-ico, .avatar-btn, .bell-btn');
  if (!icon) return;
  icon.classList.remove('icon-clicked');
  void icon.offsetWidth;
  icon.classList.add('icon-clicked');
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
  updateHeaderBalance();
  renderNotifications();
  updateBellBadge();
  applyProUI();
  GKStore.updateStreak();

  window.addEventListener('gkStateChanged', () => {
    updateHeaderBalance();
    applyProUI();
  });
});
