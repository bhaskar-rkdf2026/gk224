<?php
/**
 * GK224.COM - Shared Footer Component (footer.php)
 */
?>
  </main>
</div>

<!-- FIXED BACK BAR -->
<div class="back-bar" style="position:fixed;bottom:0;left:50%;transform:translateX(-50%);width:100%;max-width:480px;padding:10px 24px 20px;background:linear-gradient(to top, rgba(10,10,15,0.98) 60%, transparent);z-index:99;pointer-events:none;">
  <a href="javascript:void(0)" class="back-bar-btn" onclick="doLogout()" style="display:flex;align-items:center;justify-content:center;gap:7px;width:100%;padding:11px;background:rgba(255,255,255,0.04);border:1px solid var(--border);border-radius:50px;color:var(--muted);font-size:0.82rem;text-decoration:none;pointer-events:all;">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/></svg>
    Logout
  </a>
</div>

<!-- TOAST NOTIFICATION CONTAINER -->
<div class="toast" id="toast"></div>

<!-- SHARED MODALS -->
<?php include __DIR__ . '/modals.php'; ?>

<!-- SCRIPTS -->
<script src="assets/js/store.js"></script>
<script src="assets/js/nav.js"></script>
<script src="assets/js/auth.js"></script>
<script src="assets/js/pay.js"></script>
<script src="assets/js/scratch.js"></script>
<script src="assets/js/earn.js"></script>

<script>
  // Enforce Auth Modal gate
  if ((!localStorage.getItem('gk_auth_logged_in') || localStorage.getItem('gk_auth_logged_in') !== 'true') && !window.GK_USER_LOGGED_IN) {
    if (typeof openAuthModal === 'function') {
      openAuthModal(true);
    }
  }

  // Welcome popup trigger for sequential post-login entry
  if (new URLSearchParams(window.location.search).get('welcome') === '1') {
    setTimeout(function() {
      const pop = document.getElementById('welcomePopup');
      if (pop) {
        spawnConfetti();
        pop.classList.add('show');
      }
    }, 400);
  }
</script>
</body>
</html>
