<?php
$pageTitle = 'GK224.COM — Payments & Wallet';
$activePage = 'pay';
$showBalanceCard = true;
include __DIR__ . '/includes/header.php';
?>

<!-- ===== PAY SECTION ===== -->
<section id="pay" class="section active">
  <div class="sec-head">
    <div class="sec-title">Payments</div>
    <div class="sec-sub">Send, request &amp; manage money</div>
  </div>

  <!-- GK PRO OFFER BANNER -->
  <div class="pro-banner" id="proBanner" style="display:none" onclick="openProModal()">
    <div class="pro-banner-ico"><i class="ri-vip-crown-2-fill"></i></div>
    <div class="pro-banner-body">
      <div class="pro-banner-title">Go GK Pro — 2x Cashback, ₹0 Fees</div>
      <div class="pro-banner-sub">Upgrade for priority transfers &amp; double points</div>
    </div>
    <div class="pro-banner-cta">Upgrade</div>
  </div>

  <!-- ACTION MINI CARDS (6 CARDS GRID) -->
  <div class="grid-3">
    <div class="mini-card" onclick="openAddMoney()">
      <span class="ico"><i class="ri-add-circle-line"></i></span>
      <div class="a-title">Add Money</div>
    </div>
    <div class="mini-card" onclick="openPayForm('send')">
      <span class="ico"><i class="ri-send-plane-2-line"></i></span>
      <div class="a-title">Send</div>
    </div>
    <div class="mini-card" onclick="openPayForm('request')">
      <span class="ico"><i class="ri-download-2-line"></i></span>
      <div class="a-title">Request</div>
    </div>
    <div class="mini-card" onclick="openPayForm('bill')">
      <span class="ico"><i class="ri-file-list-3-line"></i></span>
      <div class="a-title">Bills</div>
    </div>
    <div class="mini-card" onclick="openScratchModal()">
      <span class="ico"><i class="ri-sparkling-2-line"></i></span>
      <div class="a-title">Scratchpad</div>
    </div>
    <div class="mini-card" id="referralCard" onclick="openReferral()">
      <span class="ico"><i class="ri-gift-line"></i></span>
      <div class="a-title">Refer</div>
    </div>
  </div>

  <!-- IN-LINE PAY FORM -->
  <div id="payForm" class="form-box" style="display:none">
    <div class="form-box-head" style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
      <button class="back-btn" onclick="closePayForm()" title="Back" style="margin-bottom:0;"><i class="ri-arrow-left-line"></i></button>
      <h3 id="payFormTitle" style="margin-bottom:0;">Send Money</h3>
    </div>
    <div class="form-group">
      <label class="form-label">Amount (₹)</label>
      <input type="number" id="payAmount" class="form-input" placeholder="0.00" min="1">
    </div>
    <div class="form-group">
      <label class="form-label">Description</label>
      <input type="text" id="payDesc" class="form-input" placeholder="What's this for?">
    </div>
    <div class="form-group">
      <label class="form-label">Category</label>
      <select id="payCat" class="form-select">
        <option value="">Select category</option>
        <option value="food">🍽️ Food &amp; Dining (2% Cashback)</option>
        <option value="transport">🚗 Transport (3% Cashback)</option>
        <option value="shopping">🛍️ Shopping (5% Cashback)</option>
        <option value="bills">⚡ Bills &amp; Utilities (1% Cashback)</option>
        <option value="entertainment">🎮 Entertainment (4% Cashback)</option>
        <option value="other">📦 Other</option>
      </select>
    </div>
    <button class="btn btn-primary" id="paySubmitBtn" onclick="submitPayment()">Process Payment</button>
    <button class="btn btn-ghost" onclick="closePayForm()">Cancel</button>
  </div>

  <!-- RECENT TRANSACTIONS -->
  <div class="tx-list">
    <div class="tx-list-head">Recent Transactions</div>
    <div id="payTransactions">
      <div class="empty"><div class="empty-ico"><i class="ri-bank-card-2-line"></i></div><p>No transactions yet</p></div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
