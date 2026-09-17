/* ==========================================================================
   GK224.COM - Authentication & Session Engine (auth.js)
   Sequential Auth Gate: Full-view Auth Modal + login.php integration
   ========================================================================== */

function getRegisteredUsers() {
  const defaults = {
    'demo@gk224.com': { password: 'demo1234', name: 'Demo User', phone: '+91 90000 00000' }
  };
  try {
    const saved = localStorage.getItem('gk_registered_users');
    return saved ? Object.assign({}, defaults, JSON.parse(saved)) : defaults;
  } catch(e) {
    return defaults;
  }
}

function saveRegisteredUser(key, userObj) {
  const users = getRegisteredUsers();
  users[key] = userObj;
  try {
    localStorage.setItem('gk_registered_users', JSON.stringify(users));
  } catch(e) {}
}

function normalizeKey(v) { return (v || '').trim().toLowerCase(); }

function markLoggedIn(userData) {
  // 1. Update user profile store
  GKStore.setUser(userData);
  
  // 2. Set client local auth flag
  localStorage.setItem('gk_auth_logged_in', 'true');
  window.GK_USER_LOGGED_IN = true;
  document.documentElement.classList.remove('auth-required');
  
  // 3. Set cookie for PHP server recognition (10 days)
  document.cookie = "gk_logged_in=1; path=/; max-age=864000; SameSite=Lax";

  // 4. Sync session with backend API
  try {
    const fd = new FormData();
    fd.append('action', 'login');
    fd.append('email', userData.email || 'demo@gk224.com');
    fd.append('password', 'demo1234');
    fetch('api/auth.php', { method: 'POST', body: fd }).catch(() => {});
  } catch(e) {}
}

function togglePwd(id, el) {
  const inp = document.getElementById(id);
  if (!inp) return;
  if (inp.type === 'password') {
    inp.type = 'text';
    el.innerHTML = '<i class="ri-eye-off-line"></i>';
  } else {
    inp.type = 'password';
    el.innerHTML = '<i class="ri-eye-line"></i>';
  }
}

function switchAuth(tab) {
  document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.auth-page').forEach(p => p.classList.remove('active'));
  const tabEl = document.getElementById('tab' + tab.charAt(0).toUpperCase() + tab.slice(1));
  const pageEl = document.getElementById('auth' + tab.charAt(0).toUpperCase() + tab.slice(1));
  if (tabEl) tabEl.classList.add('active');
  if (pageEl) pageEl.classList.add('active');
}

function numOnly(el) {
  el.value = el.value.replace(/\D/g, '');
}

// ─── AUTH MODAL ENGINE (FULL VIEW GATE) ─────────────────────
let authModalLocked = true;

function openAuthModal(isLocked = true) {
  authModalLocked = isLocked;
  const modal = document.getElementById('authModal');
  const closeBtn = document.getElementById('authModalCloseBtn');
  if (modal) {
    modal.classList.add('open');
    if (closeBtn) {
      closeBtn.style.display = isLocked ? 'none' : 'flex';
    }
  }
}

function closeAuthModal() {
  const modal = document.getElementById('authModal');
  if (modal) {
    modal.classList.remove('open');
  }
}

function closeAuthModalOutside(e) {
  if (e.target === document.getElementById('authModal')) {
    if (authModalLocked) {
      const sheet = document.querySelector('.auth-modal-sheet');
      if (sheet) {
        sheet.classList.remove('shake');
        void sheet.offsetWidth;
        sheet.classList.add('shake');
      }
      showToast('Pehle Login ya Quick Demo select karein 🔒', 'error');
    } else {
      closeAuthModal();
    }
  }
}

function switchModalAuth(tab) {
  const tabLogin = document.getElementById('modalTabLogin');
  const tabSignup = document.getElementById('modalTabSignup');
  const pageLogin = document.getElementById('modalAuthLogin');
  const pageSignup = document.getElementById('modalAuthSignup');
  const pageOtp = document.getElementById('modalAuthOtp');

  if (tab === 'login') {
    if (tabLogin) { tabLogin.classList.add('active'); tabLogin.style.background = 'var(--accent)'; tabLogin.style.color = '#0a0a0f'; }
    if (tabSignup) { tabSignup.classList.remove('active'); tabSignup.style.background = 'none'; tabSignup.style.color = 'var(--muted)'; }
    if (pageLogin) pageLogin.style.display = 'block';
    if (pageSignup) pageSignup.style.display = 'none';
    if (pageOtp) pageOtp.style.display = 'none';
  } else if (tab === 'signup') {
    if (tabSignup) { tabSignup.classList.add('active'); tabSignup.style.background = 'var(--accent)'; tabSignup.style.color = '#0a0a0f'; }
    if (tabLogin) { tabLogin.classList.remove('active'); tabLogin.style.background = 'none'; tabLogin.style.color = 'var(--muted)'; }
    if (pageLogin) pageLogin.style.display = 'none';
    if (pageSignup) pageSignup.style.display = 'block';
    if (pageOtp) pageOtp.style.display = 'none';
  }
}

function doModalLogin() {
  const emailRaw = document.getElementById('modalLoginEmail').value.trim();
  const pwd = document.getElementById('modalLoginPwd').value;
  if (!emailRaw) { showToast('Please enter your email or phone', 'error'); return; }
  if (!pwd) { showToast('Please enter your password', 'error'); return; }

  const key = normalizeKey(emailRaw);
  const users = getRegisteredUsers();
  const user = users[key];

  if (user && user.password !== pwd) {
    showToast('Incorrect password. Please try again.', 'error');
    return;
  }

  const userData = {
    name: (user && user.name) || (key.includes('@') ? key.split('@')[0] : 'GK Member'),
    email: key.includes('@') ? key : 'user@gk224.com',
    phone: (user && user.phone) || emailRaw
  };
  
  markLoggedIn(userData);
  closeAuthModal();
  showToast(`Welcome back, ${userData.name}! 🎉`, 'success');

  // Trigger welcome popup with confetti
  setTimeout(() => {
    const pop = document.getElementById('welcomePopup');
    if (pop && typeof spawnConfetti === 'function') {
      spawnConfetti();
      pop.classList.add('show');
    }
  }, 300);
}

function doModalSignup() {
  const first = document.getElementById('modalSignupFirst').value.trim();
  const last = document.getElementById('modalSignupLast').value.trim();
  const email = document.getElementById('modalSignupEmail').value.trim();
  const phone = document.getElementById('modalSignupPhone').value.trim();
  const pwd = document.getElementById('modalSignupPwd').value;
  const confirm = document.getElementById('modalSignupConfirm').value;

  if (!first || !last) { showToast('Please enter your full name', 'error'); return; }
  if (!email || !email.includes('@')) { showToast('Enter a valid email address', 'error'); return; }
  if (!phone) { showToast('Please enter your mobile number', 'error'); return; }
  if (pwd.length < 8) { showToast('Password must be at least 8 characters', 'error'); return; }
  if (pwd !== confirm) { showToast('Passwords do not match', 'error'); return; }

  const key = normalizeKey(email);
  const users = getRegisteredUsers();
  if (users[key]) {
    showToast('An account with this email already exists. Please login.', 'error');
    return;
  }

  const fullName = first + ' ' + last;
  saveRegisteredUser(key, { password: pwd, name: fullName, phone: phone });
  markLoggedIn({ name: fullName, email: email, phone: phone });
  closeAuthModal();
  showToast(`Account created! Welcome to GK224, ${fullName} 🎉`, 'success');

  setTimeout(() => {
    const pop = document.getElementById('welcomePopup');
    if (pop && typeof spawnConfetti === 'function') {
      spawnConfetti();
      pop.classList.add('show');
    }
  }, 300);
}

// ─── MODAL OTP FLOW ─────────────────────────────────────────
let modalPendingOtp = null;
let modalPendingPhone = null;

function startModalOtpLogin() {
  document.getElementById('modalAuthLogin').style.display = 'none';
  document.getElementById('modalAuthSignup').style.display = 'none';
  const otpPage = document.getElementById('modalAuthOtp');
  if (otpPage) otpPage.style.display = 'block';
  document.getElementById('modalOtpPhoneWrap').style.display = 'block';
  document.getElementById('modalOtpCodeWrap').style.display = 'none';
  document.getElementById('modalOtpActionBtn').textContent = 'Send OTP →';
  document.getElementById('modalOtpActionBtn').setAttribute('onclick', 'sendModalOtp()');
}

function cancelModalOtpLogin() {
  switchModalAuth('login');
}

function sendModalOtp() {
  const phone = document.getElementById('modalOtpPhone').value.trim();
  if (!phone || phone.replace(/\D/g,'').length < 10) {
    showToast('Enter a valid 10-digit mobile number', 'error');
    return;
  }
  modalPendingPhone = phone;
  modalPendingOtp = String(Math.floor(1000 + Math.random() * 9000));
  showToast(`Demo OTP sent: ${modalPendingOtp}`, 'success');
  document.getElementById('modalOtpPhoneWrap').style.display = 'none';
  document.getElementById('modalOtpCodeWrap').style.display = 'block';
  document.getElementById('modalOtpStepDesc').textContent = `OTP sent to ${phone}`;
  document.getElementById('modalOtpActionBtn').textContent = 'Verify & Login →';
  document.getElementById('modalOtpActionBtn').setAttribute('onclick', 'verifyModalOtp()');
}

function verifyModalOtp() {
  const code = document.getElementById('modalOtpCode').value.trim();
  if (!code) { showToast('Enter the OTP sent to your phone', 'error'); return; }
  if (code !== modalPendingOtp) { showToast('Incorrect OTP. Please try again.', 'error'); return; }

  showToast('Verifying...', 'success');
  markLoggedIn({ name: 'Mobile User', phone: modalPendingPhone, email: 'mobile@gk224.com' });
  closeAuthModal();
  showToast('Logged in with OTP! Welcome 👋', 'success');

  setTimeout(() => {
    const pop = document.getElementById('welcomePopup');
    if (pop && typeof spawnConfetti === 'function') {
      spawnConfetti();
      pop.classList.add('show');
    }
  }, 300);
}

// ─── ENVIRONMENT AWARE URL HELPER ─────────────────────────
function getAppUrl(page) {
  const isStatic = window.location.pathname.endsWith('.html') || 
                   (!window.location.pathname.includes('.php') && !window.location.origin.includes('localhost'));
  if (page === 'home') {
    return isStatic ? 'index.html?welcome=1' : 'index.php?welcome=1';
  }
  if (page === 'login') {
    return isStatic ? 'login.html' : 'login.php';
  }
  if (page === 'logout') {
    return isStatic ? 'login.html?logged_out=1' : 'logout.php';
  }
  return page;
}

// ─── QUICK DEMO LOGIN (WORKS ANYWHERE) ──────────────────────
function quickDemoLogin() {
  const demoData = {
    name: 'Demo User',
    email: 'demo@gk224.com',
    phone: '+91 90000 00000'
  };
  markLoggedIn(demoData);
  
  if (document.getElementById('authModal')) {
    closeAuthModal();
    showToast('Demo Access Granted! Welcome to GK224 ⚡', 'success');
    setTimeout(() => {
      const pop = document.getElementById('welcomePopup');
      if (pop && typeof spawnConfetti === 'function') {
        spawnConfetti();
        pop.classList.add('show');
      }
    }, 300);
  } else {
    // If on standalone login page
    showToast('Logging in as Demo User...', 'success');
    setTimeout(() => {
      window.location.href = getAppUrl('home');
    }, 600);
  }
}

// ─── STANDALONE LOGIN.PHP FUNCTIONS ─────────────────────────
function doLogin() {
  const emailRaw = document.getElementById('loginEmail').value.trim();
  const pwd = document.getElementById('loginPwd').value;
  if (!emailRaw) { showToast('Please enter your email or phone', 'error'); return; }
  if (!pwd) { showToast('Please enter your password', 'error'); return; }

  const key = normalizeKey(emailRaw);
  const users = getRegisteredUsers();
  const user = users[key];

  if (user && user.password !== pwd) {
    showToast('Incorrect password. Please try again.', 'error');
    return;
  }

  showToast('Logging you in...', 'success');
  const userData = {
    name: (user && user.name) || (key.includes('@') ? key.split('@')[0] : 'GK Member'),
    email: key.includes('@') ? key : 'user@gk224.com',
    phone: (user && user.phone) || emailRaw
  };
  markLoggedIn(userData);

  setTimeout(() => {
    window.location.href = getAppUrl('home');
  }, 700);
}

function doSignup() {
  const first = document.getElementById('signupFirst').value.trim();
  const last = document.getElementById('signupLast').value.trim();
  const email = document.getElementById('signupEmail').value.trim();
  const phone = document.getElementById('signupPhone').value.trim();
  const pwd = document.getElementById('signupPwd').value;
  const confirm = document.getElementById('signupConfirm').value;

  if (!first || !last) { showToast('Please enter your full name', 'error'); return; }
  if (!email || !email.includes('@')) { showToast('Enter a valid email address', 'error'); return; }
  if (!phone) { showToast('Please enter your mobile number', 'error'); return; }
  if (pwd.length < 8) { showToast('Password must be at least 8 characters', 'error'); return; }
  if (pwd !== confirm) { showToast('Passwords do not match', 'error'); return; }

  const key = normalizeKey(email);
  const users = getRegisteredUsers();
  if (users[key]) {
    showToast('An account with this email already exists. Please login.', 'error');
    return;
  }

  showToast('Creating your account...', 'success');
  const fullName = first + ' ' + last;
  saveRegisteredUser(key, { password: pwd, name: fullName, phone: phone });
  markLoggedIn({ name: fullName, email: email, phone: phone });

  setTimeout(() => {
    window.location.href = getAppUrl('home');
  }, 800);
}

function googleLogin() {
  showToast('Connecting to Google...', 'success');
  markLoggedIn({ name: 'Google User', email: 'google.user@gmail.com' });
  
  if (document.getElementById('authModal')) {
    closeAuthModal();
    showToast('Logged in with Google! Welcome 🎉', 'success');
    setTimeout(() => {
      const pop = document.getElementById('welcomePopup');
      if (pop && typeof spawnConfetti === 'function') {
        spawnConfetti();
        pop.classList.add('show');
      }
    }, 300);
  } else {
    setTimeout(() => {
      window.location.href = getAppUrl('home');
    }, 800);
  }
}

function doLogout() {
  if (!confirm('Are you sure you want to logout?')) return;
  
  localStorage.removeItem('gk_auth_logged_in');
  document.cookie = "gk_logged_in=; path=/; max-age=0";
  window.GK_USER_LOGGED_IN = false;
  document.documentElement.classList.add('auth-required');

  try {
    fetch('api/auth.php?action=logout').catch(() => {});
  } catch(e) {}

  showToast('Logged out safely. Please login to continue 👋', 'success');
  
  // If we are inside the views, open the Auth Modal in full view
  if (document.getElementById('authModal')) {
    openAuthModal(true);
  } else {
    setTimeout(() => {
      window.location.href = getAppUrl('logout');
    }, 400);
  }
}

// ─── INITIALIZATION ────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
  const isLocalAuth = localStorage.getItem('gk_auth_logged_in') === 'true';
  const isServerAuth = window.GK_USER_LOGGED_IN === true;
  const isAuth = isLocalAuth || isServerAuth;

  if (!isAuth && !window.location.pathname.includes('login.php')) {
    openAuthModal(true);
  }
});
