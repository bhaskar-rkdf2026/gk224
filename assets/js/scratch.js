/* ==========================================================================
   GK224.COM - Lucky Scratchpad & Rewards Engine (scratch.js)
   ========================================================================== */

let currentActiveCardId = 'sc_welcome';
let isScratching = false;

function openScratchModal() {
  const cards = GKStore.getScratchCards();
  const firstUnscratched = cards.find(c => c.status === 'unscratched');
  if (firstUnscratched) {
    currentActiveCardId = firstUnscratched.id;
  } else if (cards.length > 0) {
    currentActiveCardId = cards[0].id;
  }
  renderScratchUI();
  const modal = document.getElementById('scratchModal');
  if (modal) modal.classList.add('open');
  setupScratchEvents();
  setTimeout(() => {
    const active = cards.find(c => c.id === currentActiveCardId) || cards[0];
    if (active) initScratchCanvas(active);
  }, 150);
}

function closeScratchModal() {
  const modal = document.getElementById('scratchModal');
  if (modal) modal.classList.remove('open');
  isScratching = false;
}

function closeModalOutsideScratch(e) {
  if (e.target === document.getElementById('scratchModal')) closeScratchModal();
}

function renderScratchUI() {
  const cards = GKStore.getScratchCards();
  const readyCards = cards.filter(c => c.status === 'unscratched');
  const claimedCards = cards.filter(c => c.status === 'claimed');
  const totalWon = claimedCards.reduce((sum, c) => sum + (c.reward || 0), 0);

  const countEl = document.getElementById('scAvailableCount');
  const wonEl = document.getElementById('scTotalWon');
  if (countEl) countEl.textContent = readyCards.length;
  if (wonEl) wonEl.textContent = `₹${totalWon.toLocaleString('en-IN')}`;

  const activeCard = cards.find(c => c.id === currentActiveCardId) || cards[0];
  if (activeCard) {
    const badgeEl = document.getElementById('activeScratchBadge');
    const titleEl = document.getElementById('activeScratchTitle');
    const srbBox = document.getElementById('scratchRewardBox');

    if (badgeEl) {
      badgeEl.textContent = activeCard.status === 'claimed' ? 'Claimed' : 'Ready to Scratch';
      badgeEl.className = activeCard.status === 'claimed' ? 'scratch-card-badge sc-badge-claimed' : 'scratch-card-badge sc-badge-ready';
    }
    if (titleEl) titleEl.textContent = activeCard.title;
    if (srbBox) srbBox.classList.toggle('claimed-bg', activeCard.status === 'claimed');

    initScratchCanvas(activeCard);
  }

  const listEl = document.getElementById('scratchCardsList');
  if (listEl) {
    listEl.innerHTML = cards.map(c => {
      const isAct = c.id === currentActiveCardId;
      const isDone = c.status === 'claimed';
      return `
        <div class="sc-item-card ${isAct ? 'active-card' : ''} ${isDone ? 'sc-done' : ''}" onclick="selectScratchCard('${c.id}')">
          <div class="sc-item-top">
            <div class="sc-item-ico"><i class="${c.icon || 'ri-gift-2-line'}"></i></div>
            <span class="sc-item-badge ${isDone ? 'sc-badge-claimed' : 'sc-badge-ready'}">${isDone ? 'Won ₹' + c.reward : 'Unscratched'}</span>
          </div>
          <div class="sc-item-title">${c.title}</div>
          <div class="sc-item-desc">${c.desc}</div>
        </div>
      `;
    }).join('');
  }
}

function selectScratchCard(id) {
  currentActiveCardId = id;
  renderScratchUI();
}

function claimDailyScratchCard() {
  const today = new Date().toDateString();
  const lastDaily = localStorage.getItem('gk_last_daily_scratch');
  if (lastDaily === today) {
    showToast('Daily scratch card already claimed today! Check back tomorrow 🎁', 'error');
    return;
  }
  localStorage.setItem('gk_last_daily_scratch', today);
  const amounts = [10, 15, 20, 25, 50, 100];
  const randAmt = amounts[Math.floor(Math.random() * amounts.length)];
  const newCard = {
    id: 'sc_daily_' + Date.now(),
    title: 'Daily Bonus Card',
    desc: 'Claimed today · Win cash',
    reward: randAmt,
    status: 'unscratched',
    icon: 'ri-calendar-check-line'
  };
  GKStore.addScratchCard(newCard);
  currentActiveCardId = newCard.id;
  renderScratchUI();
  showToast('New daily scratch card added! 🎁 Scratch to reveal cash.', 'success');
}

function grantNewScratchCard(title, rewardAmount, icon) {
  const newCard = {
    id: 'sc_' + Date.now() + Math.random().toString(36).substr(2,4),
    title: title || 'Lucky Bonus Card',
    desc: 'Special reward · Win cash',
    reward: rewardAmount || 20,
    status: 'unscratched',
    icon: icon || 'ri-sparkling-2-line'
  };
  GKStore.addScratchCard(newCard);
  showToast(`🎁 You earned a new Scratch Card: "${title}"!`, 'success');
  addNotification('New Scratch Card Earned! 🎁', `You unlocked a Lucky Scratch Card: "${title}". Scratch to win up to ₹${rewardAmount}!`, 'ri-sparkling-2-line');
}

function initScratchCanvas(card) {
  const canvas = document.getElementById('scratchCanvasBig');
  if (!canvas) return;
  const ctx = canvas.getContext('2d');
  const w = canvas.width;
  const h = canvas.height;

  canvas.classList.remove('cleared');

  if (card.status === 'claimed') {
    canvas.classList.add('cleared');
    document.getElementById('srbAmount').textContent = `+₹${card.reward}`;
    document.getElementById('srbLabel').textContent = `Claimed: ₹${card.reward} in your wallet`;
    document.getElementById('scratchInstructions').innerHTML = `<i class="ri-check-double-line" style="color:var(--green)"></i> Card already scratched and claimed!`;
    return;
  }

  document.getElementById('srbAmount').textContent = `+₹${card.reward}`;
  document.getElementById('srbLabel').textContent = `Instant Cash Added to Wallet!`;
  document.getElementById('scratchInstructions').innerHTML = `<i class="ri-hand-coin-line"></i> Rub or drag finger over the card to scratch!`;

  ctx.globalCompositeOperation = 'source-over';
  const grad = ctx.createLinearGradient(0, 0, w, h);
  grad.addColorStop(0, '#2d3038');
  grad.addColorStop(0.25, '#4b4e5a');
  grad.addColorStop(0.5, '#2f323c');
  grad.addColorStop(0.75, '#565a68');
  grad.addColorStop(1, '#20222a');
  ctx.fillStyle = grad;
  ctx.fillRect(0, 0, w, h);

  ctx.strokeStyle = 'rgba(181,212,0,0.22)';
  ctx.lineWidth = 2;
  ctx.strokeRect(10, 10, w - 20, h - 20);

  ctx.strokeStyle = 'rgba(255,255,255,0.09)';
  ctx.lineWidth = 1;
  ctx.strokeRect(16, 16, w - 32, h - 32);

  ctx.textAlign = 'center';
  ctx.fillStyle = '#b5d400';
  ctx.font = 'bold 22px "Syne", sans-serif';
  ctx.fillText('GK224 REWARD', w / 2, h / 2 - 10);

  ctx.fillStyle = '#d0d0dc';
  ctx.font = '600 13px "DM Sans", sans-serif';
  ctx.fillText('✨ SCRATCH WITH FINGER OR MOUSE ✨', w / 2, h / 2 + 18);

  ctx.fillStyle = 'rgba(181,212,0,0.7)';
  ctx.font = 'bold 11px "DM Sans", sans-serif';
  ctx.fillText('Win up to ₹500 Instant Cash', w / 2, h / 2 + 40);
}

function setupScratchEvents() {
  const canvas = document.getElementById('scratchCanvasBig');
  if (!canvas || canvas.dataset.eventsBound) return;
  canvas.dataset.eventsBound = 'true';
  const ctx = canvas.getContext('2d');

  function scratchAt(x, y) {
    const cards = GKStore.getScratchCards();
    const card = cards.find(c => c.id === currentActiveCardId);
    if (!card || card.status === 'claimed') return;

    ctx.globalCompositeOperation = 'destination-out';
    ctx.beginPath();
    ctx.arc(x, y, 22, 0, Math.PI * 2);
    ctx.fill();

    checkScratchProgress(canvas, card);
  }

  function getPos(e) {
    const rect = canvas.getBoundingClientRect();
    const scaleX = canvas.width / rect.width;
    const scaleY = canvas.height / rect.height;
    if (e.touches && e.touches[0]) {
      return {
        x: (e.touches[0].clientX - rect.left) * scaleX,
        y: (e.touches[0].clientY - rect.top) * scaleY
      };
    }
    return {
      x: (e.clientX - rect.left) * scaleX,
      y: (e.clientY - rect.top) * scaleY
    };
  }

  canvas.addEventListener('mousedown', e => { isScratching = true; const p = getPos(e); scratchAt(p.x, p.y); });
  window.addEventListener('mousemove', e => { if (isScratching) { const p = getPos(e); scratchAt(p.x, p.y); } });
  window.addEventListener('mouseup', () => { isScratching = false; });

  canvas.addEventListener('touchstart', e => { isScratching = true; const p = getPos(e); scratchAt(p.x, p.y); e.preventDefault(); }, { passive: false });
  canvas.addEventListener('touchmove', e => { if (isScratching) { const p = getPos(e); scratchAt(p.x, p.y); } e.preventDefault(); }, { passive: false });
  window.addEventListener('touchend', () => { isScratching = false; });
}

function checkScratchProgress(canvas, card) {
  if (card.status === 'claimed') return;
  const ctx = canvas.getContext('2d');
  const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
  const data = imgData.data;
  let transparentCount = 0;
  const totalPixels = data.length / 4;

  for (let i = 3; i < data.length; i += 32) {
    if (data[i] === 0) transparentCount++;
  }
  const sampleTotal = totalPixels / 8;
  const pct = (transparentCount / sampleTotal) * 100;

  if (pct >= 36) {
    claimScratchCardReward(card);
  }
}

function claimScratchCardReward(card) {
  const cards = GKStore.getScratchCards();
  const target = cards.find(c => c.id === card.id);
  if (!target || target.status === 'claimed') return;
  target.status = 'claimed';
  GKStore.saveScratchCards(cards);

  const canvas = document.getElementById('scratchCanvasBig');
  if (canvas) canvas.classList.add('cleared');

  GKStore.addTransaction({
    type: 'earning',
    title: 'Scratchpad Won: ' + target.title,
    amount: target.reward,
    ico: '<i class="ri-sparkling-2-line"></i>'
  });

  renderScratchUI();
  showToast(`🎉 You Won ₹${target.reward}! Added to wallet.`, 'success');
  addNotification('Scratchpad Reward Won! 🎁', `Congratulations! You scratched "${target.title}" and won ₹${target.reward}.`, 'ri-sparkling-2-line');

  if (typeof spawnConfetti === 'function') spawnConfetti();
}
