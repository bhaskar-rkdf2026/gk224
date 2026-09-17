<?php
$pageTitle = 'GK224.COM — Lucky Scratchpad & Rewards';
$activePage = 'scratchpad';
$showBalanceCard = true;
include __DIR__ . '/includes/header.php';
?>

<!-- ===== SCRATCHPAD PAGE ===== -->
<section id="scratchpad-page" class="section active">
  <div class="sec-head">
    <div class="sec-title">Lucky Scratchpad 🎁</div>
    <div class="sec-sub">Rub with your finger or mouse to reveal instant cash!</div>
  </div>

  <div class="scratch-modal-stats">
    <div class="sc-stat">
      <div class="sc-stat-num" id="scPageAvailableCount">0</div>
      <div class="sc-stat-lbl">Cards Ready</div>
    </div>
    <div class="sc-stat">
      <div class="sc-stat-num" id="scPageTotalWon">₹0</div>
      <div class="sc-stat-lbl">Total Won</div>
    </div>
  </div>

  <!-- INTERACTIVE SCRATCH ARENA -->
  <div class="scratch-arena">
    <div class="scratch-card-header">
      <span class="scratch-card-badge" id="activeScratchBadgePage">Daily Bonus</span>
      <span class="scratch-card-title-text" id="activeScratchTitlePage">Scratch &amp; Win</span>
    </div>

    <div class="scratch-pad-big" id="scratchPadContainerPage">
      <div class="scratch-reward-big" id="scratchRewardBoxPage">
        <div class="srb-icon"><i class="ri-money-rupee-circle-fill"></i></div>
        <div class="srb-amount" id="srbAmountPage">+₹15</div>
        <div class="srb-label" id="srbLabelPage">Instant Cash Added to Wallet!</div>
        <div class="srb-sparkles">✨ 🎉 ✨</div>
      </div>
      <canvas id="scratchCanvasBigPage" width="360" height="190"></canvas>
    </div>

    <div class="scratch-instructions" id="scratchInstructionsPage">
      <i class="ri-hand-coin-line"></i> Rub or drag finger over the card to scratch!
    </div>
  </div>

  <!-- CARDS SELECTOR -->
  <div style="margin-top:24px">
    <div class="scratch-title">
      <i class="ri-gift-2-line"></i> Your Scratch Cards
      <button class="btn-sm" style="margin-left:auto;font-size:0.72rem;padding:4px 12px;" onclick="claimDailyScratchCard()">+ Free Daily Card</button>
    </div>
    <div class="scratch-cards-grid" id="scratchCardsListPage"></div>
  </div>
</section>

<script>
  function renderPageScratchUI() {
    const cards = GKStore.getScratchCards();
    const readyCards = cards.filter(c => c.status === 'unscratched');
    const claimedCards = cards.filter(c => c.status === 'claimed');
    const totalWon = claimedCards.reduce((sum, c) => sum + (c.reward || 0), 0);

    const countEl = document.getElementById('scPageAvailableCount');
    const wonEl = document.getElementById('scPageTotalWon');
    if (countEl) countEl.textContent = readyCards.length;
    if (wonEl) wonEl.textContent = `₹${totalWon.toLocaleString('en-IN')}`;

    const activeCard = cards.find(c => c.id === currentActiveCardId) || cards[0];
    if (activeCard) {
      const badgeEl = document.getElementById('activeScratchBadgePage');
      const titleEl = document.getElementById('activeScratchTitlePage');
      const srbBox = document.getElementById('scratchRewardBoxPage');

      if (badgeEl) {
        badgeEl.textContent = activeCard.status === 'claimed' ? 'Claimed' : 'Ready to Scratch';
        badgeEl.className = activeCard.status === 'claimed' ? 'scratch-card-badge sc-badge-claimed' : 'scratch-card-badge sc-badge-ready';
      }
      if (titleEl) titleEl.textContent = activeCard.title;
      if (srbBox) srbBox.classList.toggle('claimed-bg', activeCard.status === 'claimed');

      initPageScratchCanvas(activeCard);
    }

    const listEl = document.getElementById('scratchCardsListPage');
    if (listEl) {
      listEl.innerHTML = cards.map(c => {
        const isAct = c.id === currentActiveCardId;
        const isDone = c.status === 'claimed';
        return `
          <div class="sc-item-card ${isAct ? 'active-card' : ''} ${isDone ? 'sc-done' : ''}" onclick="selectPageScratchCard('${c.id}')">
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

  function selectPageScratchCard(id) {
    currentActiveCardId = id;
    renderPageScratchUI();
  }

  function initPageScratchCanvas(card) {
    const canvas = document.getElementById('scratchCanvasBigPage');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const w = canvas.width;
    const h = canvas.height;

    canvas.classList.remove('cleared');

    if (card.status === 'claimed') {
      canvas.classList.add('cleared');
      document.getElementById('srbAmountPage').textContent = `+₹${card.reward}`;
      document.getElementById('srbLabelPage').textContent = `Claimed: ₹${card.reward} in your wallet`;
      document.getElementById('scratchInstructionsPage').innerHTML = `<i class="ri-check-double-line" style="color:var(--green)"></i> Card already scratched!`;
      return;
    }

    document.getElementById('srbAmountPage').textContent = `+₹${card.reward}`;
    document.getElementById('srbLabelPage').textContent = `Instant Cash Added to Wallet!`;
    document.getElementById('scratchInstructionsPage').innerHTML = `<i class="ri-hand-coin-line"></i> Rub or drag finger over the card to scratch!`;

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

    ctx.textAlign = 'center';
    ctx.fillStyle = '#b5d400';
    ctx.font = 'bold 22px "Syne", sans-serif';
    ctx.fillText('GK224 REWARD', w / 2, h / 2 - 10);

    ctx.fillStyle = '#d0d0dc';
    ctx.font = '600 13px "DM Sans", sans-serif';
    ctx.fillText('✨ SCRATCH WITH FINGER OR MOUSE ✨', w / 2, h / 2 + 18);
  }

  function setupPageScratchEvents() {
    const canvas = document.getElementById('scratchCanvasBigPage');
    if (!canvas || canvas.dataset.eventsBound) return;
    canvas.dataset.eventsBound = 'true';
    const ctx = canvas.getContext('2d');
    let scratching = false;

    function scratchAt(x, y) {
      const cards = GKStore.getScratchCards();
      const card = cards.find(c => c.id === currentActiveCardId);
      if (!card || card.status === 'claimed') return;

      ctx.globalCompositeOperation = 'destination-out';
      ctx.beginPath();
      ctx.arc(x, y, 22, 0, Math.PI * 2);
      ctx.fill();

      // Check progress
      const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
      const data = imgData.data;
      let transparentCount = 0;
      for (let i = 3; i < data.length; i += 32) {
        if (data[i] === 0) transparentCount++;
      }
      const pct = (transparentCount / (data.length / 32)) * 100;
      if (pct >= 36) {
        claimScratchCardReward(card);
        renderPageScratchUI();
      }
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

    canvas.addEventListener('mousedown', e => { scratching = true; const p = getPos(e); scratchAt(p.x, p.y); });
    window.addEventListener('mousemove', e => { if (scratching) { const p = getPos(e); scratchAt(p.x, p.y); } });
    window.addEventListener('mouseup', () => { scratching = false; });

    canvas.addEventListener('touchstart', e => { scratching = true; const p = getPos(e); scratchAt(p.x, p.y); e.preventDefault(); }, { passive: false });
    canvas.addEventListener('touchmove', e => { if (scratching) { const p = getPos(e); scratchAt(p.x, p.y); } e.preventDefault(); }, { passive: false });
    window.addEventListener('touchend', () => { scratching = false; });
  }

  document.addEventListener('DOMContentLoaded', function() {
    renderPageScratchUI();
    setupPageScratchEvents();
    window.addEventListener('gkStateChanged', renderPageScratchUI);
  });
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
