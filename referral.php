<?php
$pageTitle = 'GK224.COM — Refer & Win a Free Trip';
$activePage = 'referral';
$showBalanceCard = false;
include __DIR__ . '/includes/header.php';
?>

<!-- ===== REFERRAL PAGE ===== -->
<section id="referral" class="section active">
  <div class="sec-head">
    <div class="sec-title">Refer &amp; Win a Trip ✈️</div>
    <div class="sec-sub">Invite your friends &amp; climb this month's leaderboard</div>
  </div>

  <div class="ref-hero" style="text-align:center;padding:12px 0 20px;">
    <div class="ref-ico" style="width:64px;height:64px;border-radius:20px;background:rgba(181,212,0,0.15);color:var(--accent);display:flex;align-items:center;justify-content:center;font-size:2rem;margin:0 auto 12px;border:1px solid rgba(181,212,0,0.3)">
      <i class="ri-gift-2-line"></i>
    </div>
    <p style="font-size:0.85rem;color:var(--muted);max-width:340px;margin:0 auto">Top referrer when this month ends wins an all-expenses-paid trip to Nepal / Jam Gate!</p>
  </div>

  <!-- REFERRAL CODE BOX -->
  <div style="background:var(--surface);border:1.5px dashed rgba(181,212,0,0.4);border-radius:var(--r);padding:18px;text-align:center;margin-bottom:20px;">
    <div style="font-size:0.72rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--muted);margin-bottom:6px">Your Exclusive Referral Code</div>
    <div style="font-family:var(--font-head);font-size:1.8rem;font-weight:800;letter-spacing:0.08em;color:var(--accent);margin-bottom:14px;" id="refCodeDisplay">GK224XYZ</div>
    <div style="display:flex;gap:10px;">
      <button class="btn btn-primary" id="copyBtn" onclick="copyCode()"><i class="ri-clipboard-line"></i> Copy Code</button>
      <button class="btn btn-ghost" style="margin-top:0" onclick="shareReferral()"><i class="ri-share-forward-line"></i> Share Link</button>
    </div>
  </div>

  <!-- STATS STRIP -->
  <div class="stats-row" style="margin-bottom:20px">
    <div class="stat-card">
      <div class="stat-num" id="refCount">0</div>
      <div class="stat-lbl">Friends Joined</div>
    </div>
    <div class="stat-card">
      <div class="stat-num" id="friendsEarned">0</div>
      <div class="stat-lbl">Leaderboard Pts</div>
    </div>
    <div class="stat-card">
      <div class="stat-num" id="refEarned">#5</div>
      <div class="stat-lbl">Your Rank</div>
    </div>
  </div>

  <button class="btn btn-ghost" onclick="simulateFriendJoin()" style="margin-bottom:24px;border:1px solid var(--border)">
    <i class="ri-flask-line"></i> Demo: Simulate Friend Joining
  </button>

  <!-- LEADERBOARD -->
  <div class="scratch-section">
    <div class="scratch-title">
      <i class="ri-trophy-line"></i> This Month's Leaderboard
      <span class="scratch-badge">Free Trip Prize</span>
    </div>
    <p style="font-size:0.78rem;color:var(--muted);margin-bottom:14px">Top referrer at month end wins a free trip.</p>
    <div id="leaderboardList"></div>
  </div>

  <!-- REFERRAL HISTORY -->
  <div class="ref-history" id="refHistory" style="display:none;margin-top:20px">
    <div class="tx-list-head">Friends Who Joined</div>
    <div id="refFriendsList"></div>
  </div>
</section>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    updateRefStats();
    renderLeaderboard();
    const codeEl = document.getElementById('refCodeDisplay');
    if (codeEl) codeEl.textContent = MY_CODE;
  });
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
