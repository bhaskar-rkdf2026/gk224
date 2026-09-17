<?php
require_once __DIR__ . '/includes/config.php';

// If user is already logged in, redirect straight to dashboard view
$isLoggedIn = !empty($_SESSION['user_email']) || (isset($_COOKIE['gk_logged_in']) && $_COOKIE['gk_logged_in'] === '1');
if ($isLoggedIn && !isset($_GET['switch']) && !isset($_GET['logged_out'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="hi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script>
    (function() {
      var urlParams = new URLSearchParams(window.location.search);
      if (urlParams.get('logged_out') === '1') {
        localStorage.removeItem('gk_auth_logged_in');
        document.cookie = "gk_logged_in=; path=/; max-age=0";
        return;
      }
      if (localStorage.getItem('gk_auth_logged_in') === 'true' && !urlParams.get('switch')) {
        window.location.replace('index.php');
      }
    })();
  </script>
  <title>GK224.COM — Login / Sign Up</title>
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/main.css">
  <link rel="stylesheet" href="assets/css/components.css">
  <style>
    body {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      background: var(--bg);
      padding: 20px;
    }
    .auth-shell {
      width: 100%;
      max-width: 440px;
      margin: 0 auto;
      text-align: center;
    }
    .auth-brand {
      font-family: var(--font-head);
      font-size: 1.5rem;
      font-weight: 800;
      color: var(--text);
      margin-bottom: 4px;
    }
    .auth-brand span { color: var(--accent); }
    .auth-sub { font-size: 0.82rem; color: var(--muted); margin-bottom: 24px; }
    .auth-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 24px;
      padding: 28px 24px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.5);
      text-align: left;
    }
    .auth-tab-row {
      display: flex;
      background: var(--surface2);
      border-radius: 12px;
      padding: 4px;
      margin-bottom: 24px;
      gap: 4px;
    }
    .auth-tab {
      flex: 1;
      padding: 10px;
      border: none;
      border-radius: 9px;
      font-family: var(--font-body);
      font-size: 0.85rem;
      font-weight: 600;
      cursor: pointer;
      background: none;
      color: var(--muted);
      transition: all 0.25s;
    }
    .auth-tab.active {
      background: var(--accent);
      color: #0a0a0f;
      box-shadow: 0 4px 12px rgba(181,212,0,0.25);
    }
    .auth-page { display: none; }
    .auth-page.active { display: block; animation: fadeUp 0.3s ease; }
    .auth-title {
      font-family: var(--font-head);
      font-size: 1.2rem;
      font-weight: 700;
      margin-bottom: 4px;
    }
    .auth-desc { font-size: 0.78rem; color: var(--muted); margin-bottom: 20px; }
    .auth-input-wrap { position: relative; margin-bottom: 14px; }
    .auth-input-ico {
      position: absolute; left: 12px; top: 50%;
      transform: translateY(-50%); color: var(--muted); font-size: 1.1rem;
    }
    .auth-input {
      width: 100%;
      padding: 13px 14px 13px 44px;
      background: var(--surface2);
      border: 1.5px solid var(--border);
      border-radius: var(--r-sm);
      color: var(--text);
      font-family: var(--font-body);
      font-size: 0.92rem;
      outline: none;
      transition: border-color 0.2s;
    }
    .auth-input:focus {
      border-color: var(--accent);
      background: var(--surface);
    }
    .pwd-show-toggle {
      position: absolute; right: 14px; top: 50%;
      transform: translateY(-50%); cursor: pointer; color: var(--muted);
    }
    .auth-row {
      display: flex; align-items: center; justify-content: space-between;
      margin-bottom: 18px; font-size: 0.78rem;
    }
    .auth-remember { display: flex; align-items: center; gap: 6px; color: var(--muted); cursor: pointer; }
    .auth-forgot { color: var(--accent); text-decoration: none; font-weight: 500; }
    .auth-btn {
      width: 100%; padding: 14px;
      background: var(--accent); color: #0a0a0f;
      border: none; border-radius: var(--r-sm);
      font-weight: 700; font-size: 0.95rem; cursor: pointer;
      transition: all 0.2s;
      margin-bottom: 12px;
    }
    .auth-btn:hover { filter: brightness(1.1); transform: translateY(-1px); }
    .auth-divider {
      display: flex; align-items: center; gap: 10px;
      margin: 16px 0; color: var(--muted); font-size: 0.75rem;
    }
    .auth-divider::before, .auth-divider::after {
      content: ''; flex: 1; height: 1px; background: var(--border);
    }
    .auth-social { display: flex; gap: 10px; }
    .auth-social-btn {
      flex: 1; padding: 11px;
      background: var(--surface2); border: 1px solid var(--border);
      border-radius: var(--r-sm); font-size: 0.8rem;
      color: var(--text); font-weight: 600; cursor: pointer;
      display: flex; align-items: center; justify-content: center; gap: 6px;
    }
    .name-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .pwd-strength { display: flex; gap: 4px; margin-top: 6px; }
    .pwd-bar { flex: 1; height: 3px; border-radius: 2px; background: var(--border); }
    .pwd-bar.weak { background: var(--red); }
    .pwd-bar.medium { background: var(--accent3); }
    .pwd-bar.strong { background: var(--green); }
    .pwd-label { font-size: 0.7rem; color: var(--muted); margin-top: 4px; }
  </style>
</head>
<body>

<div class="auth-shell">
  <div class="auth-brand">GK Group <span>&amp;</span> Company</div>
  <div class="auth-sub">Your gateway to Pay · Learn · Earn · Travel</div>

  <div class="auth-card">
    <!-- QUICK DEMO ACCESS BANNER -->
    <div style="background:rgba(181,212,0,0.08);border:1px dashed var(--accent);border-radius:14px;padding:12px 14px;margin-bottom:18px;display:flex;align-items:center;justify-content:space-between;gap:10px;">
      <div style="text-align:left;">
        <div style="font-size:0.85rem;font-weight:700;color:var(--text);display:flex;align-items:center;gap:6px;">
          <i class="ri-flashlight-fill" style="color:var(--accent)"></i> Quick Demo Access
        </div>
        <div style="font-size:0.75rem;color:var(--muted)">1-click instant login to explore</div>
      </div>
      <button type="button" class="auth-btn" onclick="quickDemoLogin()" style="width:auto;padding:8px 14px;margin-bottom:0;font-size:0.8rem;white-space:nowrap;">
        Enter Demo →
      </button>
    </div>

    <!-- TABS -->
    <div class="auth-tab-row">
      <button class="auth-tab active" id="tabLogin" onclick="switchAuth('login')">Login</button>
      <button class="auth-tab" id="tabSignup" onclick="switchAuth('signup')">Sign Up</button>
    </div>

    <!-- LOGIN FORM -->
    <div class="auth-page active" id="authLogin">
      <div class="auth-title">Welcome back <i class="ri-hand-coin-line" style="color:var(--accent)"></i></div>
      <div class="auth-desc">Login to your GK account</div>

      <div class="auth-input-wrap">
        <span class="auth-input-ico"><i class="ri-mail-line"></i></span>
        <input type="email" class="auth-input" id="loginEmail" placeholder="Email or Phone number" value="demo@gk224.com">
      </div>

      <div class="auth-input-wrap">
        <span class="auth-input-ico"><i class="ri-lock-2-line"></i></span>
        <input type="password" class="auth-input" id="loginPwd" placeholder="Password" value="demo1234">
        <span class="pwd-show-toggle" onclick="togglePwd('loginPwd', this)"><i class="ri-eye-line"></i></span>
      </div>

      <div class="auth-row">
        <label class="auth-remember">
          <input type="checkbox" checked> Remember me
        </label>
        <a href="javascript:void(0)" class="auth-forgot" onclick="showToast('Password reset link sent to your email 📧', 'success')">Forgot password?</a>
      </div>

      <button class="auth-btn" onclick="doLogin()">Login to Account →</button>

      <div class="auth-divider">or continue with</div>
      <div class="auth-social">
        <button class="auth-social-btn" onclick="googleLogin()"><i class="ri-google-fill" style="color:#ea4335"></i> Google</button>
        <button class="auth-social-btn" onclick="startOtpLogin()"><i class="ri-smartphone-line" style="color:var(--accent)"></i> OTP</button>
      </div>
    </div>

    <!-- OTP LOGIN PANEL -->
    <div class="auth-page" id="authOtp">
      <div class="auth-title">Login with OTP <i class="ri-shield-check-line" style="color:var(--accent)"></i></div>
      <div class="auth-desc" id="otpStepDesc">Enter your mobile number to receive an OTP</div>

      <div class="auth-input-wrap" id="otpPhoneWrap">
        <span class="auth-input-ico"><i class="ri-smartphone-line"></i></span>
        <input type="tel" class="auth-input" id="otpPhone" placeholder="+91 XXXXX XXXXX" maxlength="10" oninput="numOnly(this)">
      </div>

      <div class="auth-input-wrap" id="otpCodeWrap" style="display:none">
        <span class="auth-input-ico"><i class="ri-lock-2-line"></i></span>
        <input type="text" class="auth-input" id="otpCode" placeholder="Enter 4-digit OTP" maxlength="4" oninput="numOnly(this)">
      </div>

      <button class="auth-btn" id="otpActionBtn" onclick="sendOtp()">Send OTP →</button>
      <div class="auth-row" style="justify-content:center;margin-top:14px">
        <a href="javascript:void(0)" class="auth-forgot" onclick="cancelOtpLogin()">← Back to password login</a>
      </div>
    </div>

    <!-- SIGNUP FORM -->
    <div class="auth-page" id="authSignup">
      <div class="auth-title">Create Account <i class="ri-sparkling-2-line" style="color:var(--accent)"></i></div>
      <div class="auth-desc">Join GK Group &amp; Company today</div>

      <div class="name-row">
        <div class="auth-input-wrap">
          <span class="auth-input-ico"><i class="ri-user-line"></i></span>
          <input type="text" class="auth-input" id="signupFirst" placeholder="First name">
        </div>
        <div class="auth-input-wrap">
          <span class="auth-input-ico"><i class="ri-user-line"></i></span>
          <input type="text" class="auth-input" id="signupLast" placeholder="Last name">
        </div>
      </div>

      <div class="auth-input-wrap">
        <span class="auth-input-ico"><i class="ri-mail-line"></i></span>
        <input type="email" class="auth-input" id="signupEmail" placeholder="Email address">
      </div>

      <div class="auth-input-wrap">
        <span class="auth-input-ico"><i class="ri-smartphone-line"></i></span>
        <input type="tel" class="auth-input" id="signupPhone" placeholder="Mobile number (+91)" maxlength="10" oninput="numOnly(this)">
      </div>

      <div class="auth-input-wrap">
        <span class="auth-input-ico"><i class="ri-lock-2-line"></i></span>
        <input type="password" class="auth-input" id="signupPwd" placeholder="Password (min 8 chars)" oninput="checkSignupPwd(this.value)">
        <span class="pwd-show-toggle" onclick="togglePwd('signupPwd', this)"><i class="ri-eye-line"></i></span>
      </div>
      <div class="pwd-strength" style="margin-bottom:4px">
        <div class="pwd-bar" id="spb1"></div>
        <div class="pwd-bar" id="spb2"></div>
        <div class="pwd-bar" id="spb3"></div>
        <div class="pwd-bar" id="spb4"></div>
      </div>
      <div class="pwd-label" id="signupPwdLabel" style="margin-bottom:12px"></div>

      <div class="auth-input-wrap">
        <span class="auth-input-ico"><i class="ri-lock-2-line"></i></span>
        <input type="password" class="auth-input" id="signupConfirm" placeholder="Confirm password">
        <span class="pwd-show-toggle" onclick="togglePwd('signupConfirm', this)"><i class="ri-eye-line"></i></span>
      </div>

      <div class="auth-input-wrap">
        <span class="auth-input-ico"><i class="ri-gift-line"></i></span>
        <input type="text" class="auth-input" id="signupRef" placeholder="Referral code (optional)">
      </div>

      <button class="auth-btn" onclick="doSignup()">Create My Account →</button>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>

<script src="assets/js/store.js"></script>
<script src="assets/js/nav.js"></script>
<script src="assets/js/auth.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    if (params.get('logged_out') === '1') {
      showToast('You have logged out safely 👋', 'success');
    } else if (params.get('required') === '1') {
      showToast('Pehle Login karein, phir Dashboard view open hoga 🔒', 'error');
    }
  });
</script>
</body>
</html>
