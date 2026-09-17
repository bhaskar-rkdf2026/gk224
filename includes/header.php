<?php
/**
 * GK224.COM - Reusable Header Component
 * @var string $pageTitle Current Page Title
 * @var string $activePage Active tab key ('pay' | 'learn' | 'earn' | 'travel' | 'profile' | 'referral' | 'scratchpad')
 */
require_once __DIR__ . '/config.php';

// Authentication state check
$isLoggedIn = !empty($_SESSION['user_email']) || (isset($_COOKIE['gk_logged_in']) && $_COOKIE['gk_logged_in'] === '1');

if (!isset($pageTitle)) $pageTitle = 'GK224.COM — Pay · Learn · Earn · Travel';
if (!isset($activePage)) $activePage = 'pay';
?>
<!DOCTYPE html>
<html lang="hi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script>
    window.GK_USER_LOGGED_IN = <?php echo ($isLoggedIn ? 'true' : 'false'); ?>;
    (function() {
      var isLocalAuth = localStorage.getItem('gk_auth_logged_in') === 'true';
      window.GK_IS_AUTHENTICATED = window.GK_USER_LOGGED_IN || isLocalAuth;
      if (window.GK_IS_AUTHENTICATED) {
        if (!isLocalAuth) localStorage.setItem('gk_auth_logged_in', 'true');
        if (!window.GK_USER_LOGGED_IN) document.cookie = "gk_logged_in=1; path=/; max-age=864000; SameSite=Lax";
      } else {
        document.documentElement.classList.add('auth-required');
      }
    })();
  </script>
  <title><?php echo htmlspecialchars($pageTitle); ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/main.css">
  <link rel="stylesheet" href="assets/css/components.css">
  <link rel="stylesheet" href="assets/css/scratch.css">
</head>
<body>

<div class="accent-line"></div>

<div class="shell">

  <!-- SECURITY FREEZE BANNER -->
  <div class="freeze-banner" id="freezeBanner" style="display:none">
    <i class="ri-lock-2-fill"></i>
    <span>Account frozen due to suspicious activity.</span>
    <button onclick="startUnfreeze()" id="unfreezeBtn">Verify &amp; Unfreeze</button>
  </div>

  <!-- HEADER -->
  <header class="header">
    <div class="header-top">
      <a href="index.php" class="logo">
        GK224<span>.COM</span>
        <span class="pro-badge" id="proBadge" style="display:none" onclick="openProModal()">
          <i class="ri-vip-crown-2-fill"></i> PRO
        </span>
      </a>
      <div class="header-actions">
        <div class="bell-btn" title="Notifications" onclick="openNotifications()" id="bellBtn">
          <i class="ri-notification-3-fill"></i>
          <span class="bell-badge" id="bellBadge" style="display:none">0</span>
        </div>
        <a href="profile.php" class="avatar-btn" title="Profile" id="avatarBtn">
          <i class="ri-user-3-fill"></i>
        </a>
      </div>
    </div>

    <?php if (isset($showBalanceCard) && $showBalanceCard): ?>
      <?php include __DIR__ . '/balance_card.php'; ?>
    <?php endif; ?>

    <!-- NAVIGATION TABS -->
    <nav class="nav">
      <a href="index.php" class="nav-btn <?php echo ($activePage === 'pay') ? 'active' : ''; ?>">
        <span class="nav-icon"><i class="ri-bank-card-2-line"></i></span> Pay
      </a>
      <a href="learn.php" class="nav-btn <?php echo ($activePage === 'learn') ? 'active' : ''; ?>">
        <span class="nav-icon"><i class="ri-book-open-line"></i></span> Learn
      </a>
      <a href="earn.php" class="nav-btn <?php echo ($activePage === 'earn') ? 'active' : ''; ?>">
        <span class="nav-icon"><i class="ri-coins-line"></i></span> Earn
      </a>
      <a href="travel.php" class="nav-btn <?php echo ($activePage === 'travel') ? 'active' : ''; ?>">
        <span class="nav-icon"><i class="ri-flight-takeoff-line"></i></span> Travel
      </a>
    </nav>
  </header>

  <!-- MAIN CONTENT CONTAINER -->
  <main class="main">
