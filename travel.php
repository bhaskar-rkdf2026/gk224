<?php
$pageTitle = 'GK224.COM — Travel Deals';
$activePage = 'travel';
$showBalanceCard = false;
include __DIR__ . '/includes/header.php';
?>

<!-- ===== TRAVEL SECTION ===== -->
<section id="travel" class="section active">
  <div class="sec-head">
    <div class="sec-title">Travel Deals</div>
    <div class="sec-sub">Student friendly curated group trips</div>
  </div>

  <!-- STATS ROW -->
  <div class="stats-row-2">
    <div class="stat-card">
      <div class="stat-num" id="tripsPlanned">0</div>
      <div class="stat-lbl">Trips Booked</div>
    </div>
    <div class="stat-card">
      <div class="stat-num" id="moneySaved">₹0</div>
      <div class="stat-lbl">Money Saved</div>
    </div>
  </div>

  <!-- TRIP 1 -->
  <div class="travel-card">
    <div class="travel-dest">🏔️ Jam Gate Adventure</div>
    <div class="travel-info">Weekend camping, mountain views, stargazing &amp; bonfire for students.</div>
    <div class="travel-foot">
      <div class="travel-price">₹15,000 <small>/person</small></div>
      <button class="btn-sm" onclick="openTravelModal('Jam Gate Adventure', 15000, 'Weekend camping, mountain views &amp; bonfire')">Book Now →</button>
    </div>
  </div>

  <!-- TRIP 2 -->
  <div class="travel-card">
    <div class="travel-dest">🏰 Maheshwar Heritage</div>
    <div class="travel-info">Fort tour, Narmada ghat sunset boating &amp; handloom weaving experience.</div>
    <div class="travel-foot">
      <div class="travel-price">₹12,000 <small>/person</small></div>
      <button class="btn-sm" onclick="openTravelModal('Maheshwar Heritage', 12000, 'Fort tour, Narmada ghat boating &amp; weaving')">Book Now →</button>
    </div>
  </div>

  <!-- TRIP 3 -->
  <div class="travel-card">
    <div class="travel-dest">✈️ Nepal Explorer</div>
    <div class="travel-info">Kathmandu, Pokhara sunrise, Himalayan trekking &amp; student expedition.</div>
    <div class="travel-foot">
      <div class="travel-price">₹20,000 <small>/person</small></div>
      <button class="btn-sm" onclick="openTravelModal('Nepal Explorer', 20000, 'Kathmandu, Pokhara sunrise &amp; Himalayan trek')">Book Now →</button>
    </div>
  </div>
</section>

<script>
  function openTravelModal(dest, price, info) {
    const bal = GKStore.getBalance();
    if (price > bal) {
      showToast('Insufficient wallet balance to book this trip', 'error');
      return;
    }
    openConfirmModal({
      icon: 'ri-flight-takeoff-line',
      title: 'Confirm Booking',
      sub: 'Review trip details before booking',
      itemIcon: '<i class="ri-map-pin-line"></i>',
      itemTitle: dest,
      itemDesc: info,
      priceLabel: 'Total Amount',
      price: `₹${price.toLocaleString('en-IN')}`,
      confirmLabel: `Confirm & Book ₹${price.toLocaleString('en-IN')}`,
      onConfirm: function() {
        GKStore.addTransaction({
          type: 'travel',
          title: 'Booked Trip: ' + dest,
          amount: -price,
          category: 'travel',
          ico: '<i class="ri-flight-takeoff-line"></i>'
        });
        autoCashback(price, 'travel', dest);
        showToast(`Trip Booked: ${dest}! ✈️`, 'success');
        addNotification('Trip Booking Confirmed ✈️', `Your trip "${dest}" is confirmed. Check details in your transaction ledger!`, 'ri-flight-takeoff-line');
        renderTravelStats();
      }
    });
  }

  function renderTravelStats() {
    const txs = GKStore.getTransactions();
    const trips = txs.filter(t => t.type === 'travel');
    const plannedEl = document.getElementById('tripsPlanned');
    const savedEl = document.getElementById('moneySaved');
    if (plannedEl) plannedEl.textContent = trips.length;
    if (savedEl) {
      const saved = Math.round(trips.reduce((s, t) => s + Math.abs(t.amount) * 0.1, 0));
      savedEl.textContent = `₹${saved.toLocaleString('en-IN')}`;
    }
  }

  document.addEventListener('DOMContentLoaded', function() {
    renderTravelStats();
    window.addEventListener('gkStateChanged', renderTravelStats);
  });
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
