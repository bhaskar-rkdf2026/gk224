<?php
$pageTitle = 'GK224.COM — Earn Money';
$activePage = 'earn';
$showBalanceCard = true;
include __DIR__ . '/includes/header.php';
?>

<!-- ===== EARN SECTION ===== -->
<section id="earn" class="section active">
  <div class="sec-head">
    <div class="sec-title">Earn Money</div>
    <div class="sec-sub">Multiple student income streams &amp; gigs</div>
  </div>

  <!-- STATS ROW -->
  <div class="stats-row-2">
    <div class="stat-card">
      <div class="stat-num" id="totalEarned">₹0</div>
      <div class="stat-lbl">Total Earned</div>
    </div>
    <div class="stat-card">
      <div class="stat-num" id="thisMonth">₹0</div>
      <div class="stat-lbl">This Month</div>
    </div>
  </div>

  <!-- 5 ACTION CARDS GRID -->
  <div class="grid-2">
    <div class="action-card" onclick="openScratchModal()" style="cursor:pointer">
      <span class="ico"><i class="ri-sparkling-2-line"></i></span>
      <div class="a-title">Lucky Scratchpad</div>
      <div class="a-desc">Scratch &amp; win instant cash</div>
    </div>
    <div class="action-card" onclick="openFreelance()" style="cursor:pointer">
      <span class="ico"><i class="ri-briefcase-4-line"></i></span>
      <div class="a-title">Freelance Work</div>
      <div class="a-desc">Find client projects &amp; gigs</div>
    </div>
    <div class="action-card" onclick="openSurvey()" style="cursor:pointer">
      <span class="ico"><i class="ri-survey-line"></i></span>
      <div class="a-title">Village Surveys</div>
      <div class="a-desc">₹10 per student entry</div>
    </div>
    <a href="referral.php" class="action-card">
      <span class="ico"><i class="ri-gift-line"></i></span>
      <div class="a-title">Referrals</div>
      <div class="a-desc">Invite &amp; win a free trip ✈️</div>
    </a>
    <div class="action-card" onclick="openStationery()" style="cursor:pointer; grid-column: span 2;">
      <span class="ico"><i class="ri-shopping-bag-3-line"></i></span>
      <div class="a-title">GK224.COM Stationery Store</div>
      <div class="a-desc">Shop school supplies &amp; get 5% instant cashback</div>
    </div>
  </div>

  <!-- EARNING ACTIVITY LIST -->
  <div class="tx-list">
    <div class="tx-list-head">Earning Activity</div>
    <div id="earnTransactions">
      <div class="empty"><div class="empty-ico"><i class="ri-coins-line"></i></div><p>No earnings recorded yet</p></div>
    </div>
  </div>
</section>

<!-- ═══ FREELANCE MARKETPLACE MODAL ═══ -->
<div class="modal-overlay" id="freelanceModal" onclick="closeModalOutsideFreelance(event)">
  <div class="modal-sheet" onclick="event.stopPropagation()">
    <div class="modal-handle"></div>
    <button class="back-btn" onclick="closeFreelance()" title="Back"><i class="ri-arrow-left-line"></i></button>
    <div class="modal-title"><i class="ri-briefcase-4-line"></i> Freelance Work</div>
    <div class="modal-sub">Client projects sourced by our team — pick one up &amp; start earning</div>

    <div class="fl-stats-row">
      <div class="fl-stat">
        <div class="fl-stat-num" id="flOpenCount">0</div>
        <div class="fl-stat-lbl">Open</div>
      </div>
      <div class="fl-stat">
        <div class="fl-stat-num" id="flMineCount">0</div>
        <div class="fl-stat-lbl">My Gigs</div>
      </div>
      <div class="fl-stat">
        <div class="fl-stat-num" id="flEarnedCount">₹0</div>
        <div class="fl-stat-lbl">Potential Value</div>
      </div>
    </div>

    <div class="fl-tabs">
      <button class="fl-tab active" id="flTabOpen" onclick="switchFlTab('open')">Open Projects</button>
      <button class="fl-tab" id="flTabMine" onclick="switchFlTab('mine')">My Projects</button>
    </div>

    <div id="flProjectList"></div>
  </div>
</div>

<!-- ═══ VILLAGE SURVEY MODAL ═══ -->
<div class="modal-overlay" id="surveyModal" onclick="closeModalOutsideSurvey(event)">
  <div class="modal-sheet" onclick="event.stopPropagation()">
    <div class="modal-handle"></div>
    <button class="back-btn" onclick="closeSurvey()" title="Back"><i class="ri-arrow-left-line"></i></button>
    <div class="modal-title"><i class="ri-survey-line"></i> Village Student Survey</div>
    <div class="modal-sub">Record student data from your village — earn <strong style="color:var(--accent)">₹10 per entry</strong></div>

    <div class="survey-counter">
      <div>
        <div class="survey-counter-num" id="surveyCount">0</div>
        <div class="survey-counter-lbl">Entries submitted</div>
      </div>
      <div style="text-align:right">
        <div class="survey-counter-num" id="surveyEarned">₹0</div>
        <div class="survey-counter-lbl">Total earned</div>
      </div>
    </div>

    <div class="form-group">
      <label class="form-label">Student Name</label>
      <input type="text" id="surveyName" class="form-input" placeholder="Full name">
    </div>
    <div class="form-group">
      <label class="form-label">Age</label>
      <input type="number" id="surveyAge" class="form-input" placeholder="e.g. 14">
    </div>
    <div class="form-group">
      <label class="form-label">Class / Grade</label>
      <input type="text" id="surveyClass" class="form-input" placeholder="e.g. Class 8">
    </div>
    <div class="form-group">
      <label class="form-label">Village Name</label>
      <input type="text" id="surveyVillage" class="form-input" placeholder="Village name">
    </div>
    <div class="form-group">
      <label class="form-label">Parent / Guardian Name</label>
      <input type="text" id="surveyParent" class="form-input" placeholder="Guardian name">
    </div>
    <div class="form-group">
      <label class="form-label">Contact Number</label>
      <input type="tel" id="surveyContact" class="form-input" placeholder="+91 XXXXX XXXXX" maxlength="10">
    </div>
    <div class="form-group">
      <label class="form-label">Gender</label>
      <select id="surveyGender" class="form-select">
        <option value="">Select gender</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
      </select>
    </div>
    <button class="btn btn-primary" onclick="submitSurvey()">Submit Entry (+₹10)</button>

    <div class="tx-list" style="margin-top:20px">
      <div class="tx-list-head">Recent Survey Entries</div>
      <div id="surveyEntriesList" class="survey-entry-list"></div>
    </div>
  </div>
</div>

<!-- ═══ STATIONERY STORE MODAL ═══ -->
<div class="modal-overlay" id="stationeryModal" onclick="closeModalOutsideStationery(event)">
  <div class="modal-sheet" onclick="event.stopPropagation()">
    <div class="modal-handle"></div>
    <button class="back-btn" onclick="closeStationery()" title="Back"><i class="ri-arrow-left-line"></i></button>
    <div class="modal-title"><i class="ri-shopping-bag-3-line"></i> GK224 Stationery Store</div>
    <div class="modal-sub">Shop stationery essentials &amp; get <strong style="color:var(--accent)">5% instant cashback</strong></div>

    <div class="stationery-grid">
      <div class="stationery-item">
        <div class="ico"><i class="ri-book-2-line"></i></div>
        <div class="stationery-name">Notebook (200pg)</div>
        <div class="stationery-price">₹60</div>
        <button class="stationery-buy-btn" onclick="buyStationery('Notebook (200pg)', 60, '<i class=\'ri-book-2-line\'></i>')">Buy Now</button>
      </div>
      <div class="stationery-item">
        <div class="ico"><i class="ri-quill-pen-line"></i></div>
        <div class="stationery-name">Gel Pen Set (5pc)</div>
        <div class="stationery-price">₹40</div>
        <button class="stationery-buy-btn" onclick="buyStationery('Gel Pen Set (5pc)', 40, '<i class=\'ri-quill-pen-line\'></i>')">Buy Now</button>
      </div>
      <div class="stationery-item">
        <div class="ico"><i class="ri-palette-line"></i></div>
        <div class="stationery-name">Sketch Book</div>
        <div class="stationery-price">₹120</div>
        <button class="stationery-buy-btn" onclick="buyStationery('Sketch Book', 120, '<i class=\'ri-palette-line\'></i>')">Buy Now</button>
      </div>
      <div class="stationery-item">
        <div class="ico"><i class="ri-ruler-2-line"></i></div>
        <div class="stationery-name">Geometry Box</div>
        <div class="stationery-price">₹75</div>
        <button class="stationery-buy-btn" onclick="buyStationery('Geometry Box', 75, '<i class=\'ri-ruler-2-line\'></i>')">Buy Now</button>
      </div>
    </div>
  </div>
</div>

<script>
  function renderEarnPageStats() {
    const txs = GKStore.getTransactions();
    const earns = txs.filter(t => t.type === 'earning');
    const total = earns.reduce((sum, t) => sum + t.amount, 0);

    const totalEl = document.getElementById('totalEarned');
    const monthEl = document.getElementById('thisMonth');
    if (totalEl) totalEl.textContent = `₹${total.toLocaleString('en-IN')}`;
    if (monthEl) monthEl.textContent = `₹${total.toLocaleString('en-IN')}`;

    const listEl = document.getElementById('earnTransactions');
    if (!listEl) return;
    if (!earns.length) {
      listEl.innerHTML = '<div class="empty"><div class="empty-ico"><i class="ri-coins-line"></i></div><p>No earnings recorded yet</p></div>';
      return;
    }
    listEl.innerHTML = earns.slice(0, 10).map(t => {
      const d = new Date(t.date);
      const dateStr = `${d.getDate()} ${d.toLocaleString('en-IN', { month: 'short' })}`;
      return `
        <div class="tx-item">
          <div class="tx-left">
            <div class="tx-ico">${t.ico || '<i class="ri-coins-line"></i>'}</div>
            <div>
              <div class="tx-title">${t.title}</div>
              <div class="tx-date">${dateStr}</div>
            </div>
          </div>
          <div class="tx-amount pos">+₹${t.amount.toLocaleString('en-IN')}</div>
        </div>
      `;
    }).join('');
  }

  document.addEventListener('DOMContentLoaded', function() {
    renderEarnPageStats();
    window.addEventListener('gkStateChanged', renderEarnPageStats);
  });
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
