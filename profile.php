<?php
$pageTitle = 'GK224.COM — Profile & Settings';
$activePage = 'profile';
$showBalanceCard = false;
include __DIR__ . '/includes/header.php';
?>

<style>
  .profile-hero {
    text-align: center;
    padding: 10px 0 24px;
  }
  .avatar-wrap {
    width: 80px; height: 80px;
    border-radius: 50%;
    background: linear-gradient(155deg, rgba(181,212,0,0.25), var(--surface2));
    border: 3px solid var(--accent);
    display: flex; align-items: center; justify-content: center;
    font-size: 2.4rem;
    margin: 0 auto 12px;
    cursor: pointer;
    box-shadow: 0 10px 30px rgba(181,212,0,0.25);
  }
  .profile-name {
    font-family: var(--font-head);
    font-size: 1.35rem;
    font-weight: 800;
    margin-bottom: 2px;
  }
  .profile-meta { font-size: 0.75rem; color: var(--muted); }

  .profile-stats-strip {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--r);
    padding: 14px;
    margin-bottom: 24px;
    text-align: center;
  }
  .ps-num {
    font-family: var(--font-head);
    font-size: 1.15rem;
    font-weight: 800;
    color: var(--accent);
  }
  .ps-lbl { font-size: 0.65rem; color: var(--muted); margin-top: 2px; }

  .menu-group {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--r);
    overflow: hidden;
    margin-bottom: 16px;
  }
  .menu-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 15px 18px;
    border-bottom: 1px solid var(--border);
    cursor: pointer;
    transition: background 0.15s;
  }
  .menu-item:last-child { border-bottom: none; }
  .menu-item:hover { background: var(--surface2); }
  .menu-ico {
    width: 36px; height: 36px;
    border-radius: 10px;
    background: var(--surface2);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
    color: var(--accent);
  }
  .menu-body { flex: 1; min-width: 0; }
  .menu-title { font-size: 0.88rem; font-weight: 600; }
  .menu-sub { font-size: 0.72rem; color: var(--muted); margin-top: 1px; }
  .menu-badge {
    font-size: 0.65rem;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 10px;
    background: rgba(181,212,0,0.15);
    color: var(--accent);
  }

  .edit-panel {
    display: none;
    padding: 16px 18px 20px;
    background: var(--surface2);
    border-top: 1px solid var(--border);
  }
  .edit-panel.open { display: block; animation: fadeUp 0.25s ease; }

  .avatar-picker-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 10px;
    padding: 14px;
    background: var(--surface2);
    border-radius: var(--r-sm);
    margin-bottom: 16px;
  }
  .av-opt {
    font-size: 1.8rem;
    padding: 8px;
    border-radius: 12px;
    background: var(--surface);
    cursor: pointer;
    text-align: center;
    border: 2px solid transparent;
  }
  .av-opt:hover, .av-opt.selected {
    border-color: var(--accent);
    background: rgba(181,212,0,0.12);
  }
</style>

<!-- ===== PROFILE SECTION ===== -->
<section id="profile" class="section active">
  <div class="profile-hero">
    <div class="avatar-wrap" id="userAvatarLg" onclick="toggleAvatarPicker()">
      <span id="userAvatarEmoji">👤</span>
    </div>
    <div class="profile-name" id="userDisplayName">John Doe</div>
    <div class="profile-meta" id="userEmailMeta">john@example.com · Member since 2025</div>
  </div>

  <!-- AVATAR PICKER DRAWER -->
  <div id="avatarPicker" style="display:none">
    <div style="font-size:0.75rem;font-weight:700;color:var(--muted);margin-bottom:8px;text-transform:uppercase">Choose Avatar Emoji</div>
    <div class="avatar-picker-grid">
      <div class="av-opt" onclick="pickAvatar('👤')">👤</div>
      <div class="av-opt" onclick="pickAvatar('👨‍💻')">👨‍💻</div>
      <div class="av-opt" onclick="pickAvatar('👩‍💻')">👩‍💻</div>
      <div class="av-opt" onclick="pickAvatar('🧑‍🎓')">🧑‍🎓</div>
      <div class="av-opt" onclick="pickAvatar('🚀')">🚀</div>
      <div class="av-opt" onclick="pickAvatar('👑')">👑</div>
      <div class="av-opt" onclick="pickAvatar('💎')">💎</div>
      <div class="av-opt" onclick="pickAvatar('⚡')">⚡</div>
      <div class="av-opt" onclick="pickAvatar('🦁')">🦁</div>
      <div class="av-opt" onclick="pickAvatar('🌟')">🌟</div>
    </div>
  </div>

  <!-- STATS STRIP -->
  <div class="profile-stats-strip">
    <div>
      <div class="ps-num" id="pBalance">₹10,000</div>
      <div class="ps-lbl">Balance</div>
    </div>
    <div>
      <div class="ps-num" id="pTxCount">0</div>
      <div class="ps-lbl">Transactions</div>
    </div>
    <div>
      <div class="ps-num" id="pReferrals">0</div>
      <div class="ps-lbl">Referrals</div>
    </div>
  </div>

  <!-- PERSONAL INFO GROUP -->
  <div class="menu-group">
    <div class="menu-item" onclick="toggleEditPanel('editName')">
      <div class="menu-ico"><i class="ri-user-smile-line"></i></div>
      <div class="menu-body">
        <div class="menu-title">Full Name</div>
        <div class="menu-sub" id="pmNameSub">John Doe</div>
      </div>
      <i class="ri-arrow-right-s-line" style="color:var(--muted)"></i>
    </div>
    <div class="edit-panel" id="editName">
      <div class="form-group">
        <label class="form-label">Full Name</label>
        <input type="text" id="inputName" class="form-input" placeholder="Your Name">
      </div>
      <button class="btn btn-primary" onclick="saveName()">Save Changes</button>
    </div>

    <div class="menu-item" onclick="toggleEditPanel('editPhone')">
      <div class="menu-ico"><i class="ri-smartphone-line"></i></div>
      <div class="menu-body">
        <div class="menu-title">Mobile Number</div>
        <div class="menu-sub" id="pmPhoneSub">+91 98765 43210</div>
      </div>
      <i class="ri-arrow-right-s-line" style="color:var(--muted)"></i>
    </div>
    <div class="edit-panel" id="editPhone">
      <div class="form-group">
        <label class="form-label">Phone Number</label>
        <input type="tel" id="inputPhone" class="form-input" placeholder="+91 XXXXX XXXXX">
      </div>
      <button class="btn btn-primary" onclick="savePhone()">Update Phone</button>
    </div>

    <div class="menu-item" onclick="toggleEditPanel('editUPI')">
      <div class="menu-ico"><i class="ri-bank-card-line"></i></div>
      <div class="menu-body">
        <div class="menu-title">UPI ID</div>
        <div class="menu-sub" id="pmUPISub">Not set</div>
      </div>
      <i class="ri-arrow-right-s-line" style="color:var(--muted)"></i>
    </div>
    <div class="edit-panel" id="editUPI">
      <div class="form-group">
        <label class="form-label">Primary UPI ID</label>
        <input type="text" id="inputUPI" class="form-input" placeholder="yourname@upi">
      </div>
      <button class="btn btn-primary" onclick="saveUPI()">Save UPI</button>
    </div>
  </div>

  <!-- SECURITY & KYC GROUP -->
  <div class="menu-group">
    <div class="menu-item" onclick="toggleEditPanel('editKyc')">
      <div class="menu-ico"><i class="ri-id-card-line"></i></div>
      <div class="menu-body">
        <div class="menu-title">KYC Identity Verification</div>
        <div class="menu-sub" id="kycSub">Aadhaar / PAN verification</div>
      </div>
      <span class="menu-badge" id="kycBadge">Pending</span>
    </div>
    <div class="edit-panel" id="editKyc">
      <div class="form-group">
        <label class="form-label">Document Type</label>
        <select id="inputKycType" class="form-select">
          <option>Aadhaar Card</option>
          <option>PAN Card</option>
          <option>Student ID</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Document Number</label>
        <input type="text" id="inputKycNumber" class="form-input" placeholder="Enter ID Number">
      </div>
      <button class="btn btn-primary" id="kycSubmitBtn" onclick="submitKyc()">Submit for Verification</button>
    </div>

    <div class="menu-item" onclick="toggleEditPanel('editPin')">
      <div class="menu-ico"><i class="ri-shield-keyhole-line"></i></div>
      <div class="menu-body">
        <div class="menu-title">4-Digit Security PIN</div>
        <div class="menu-sub">For payments &amp; transfers</div>
      </div>
      <i class="ri-arrow-right-s-line" style="color:var(--muted)"></i>
    </div>
    <div class="edit-panel" id="editPin">
      <div class="form-group">
        <label class="form-label">Set 4-Digit PIN</label>
        <input type="password" id="inputPin" class="form-input" maxlength="4" placeholder="••••">
      </div>
      <div class="form-group">
        <label class="form-label">Confirm PIN</label>
        <input type="password" id="inputConfirmPin" class="form-input" maxlength="4" placeholder="••••">
      </div>
      <button class="btn btn-primary" onclick="savePIN()">Save PIN</button>
    </div>
  </div>

  <button class="btn btn-ghost" onclick="doLogout()" style="margin-bottom:30px;background:rgba(239,68,68,0.1);color:var(--red);border:1px solid rgba(239,68,68,0.3)">
    <i class="ri-logout-box-r-line"></i> Logout from GK224
  </button>
</section>

<script>
  function renderProfileData() {
    const user = GKStore.getUser();
    document.getElementById('userDisplayName').textContent = user.name || 'User';
    document.getElementById('pmNameSub').textContent = user.name || 'User';
    document.getElementById('userEmailMeta').textContent = `${user.email} · Member since ${user.since || '2025'}`;
    document.getElementById('pmPhoneSub').textContent = user.phone || 'Not set';
    document.getElementById('pmUPISub').textContent = user.upi || 'Not set';
    document.getElementById('userAvatarEmoji').textContent = user.avatar || '👤';

    const avatarBtn = document.getElementById('avatarBtn');
    if (avatarBtn) avatarBtn.innerHTML = user.avatar || '<i class="ri-user-3-fill"></i>';

    document.getElementById('pBalance').textContent = `₹${GKStore.getBalance().toLocaleString('en-IN')}`;
    document.getElementById('pTxCount').textContent = GKStore.getTransactions().length;
    document.getElementById('pReferrals').textContent = GKStore.getReferralData().count;

    if (user.kyc && user.kyc.status === 'review') {
      const badge = document.getElementById('kycBadge');
      badge.textContent = 'Under Review';
      badge.style.background = 'rgba(181,212,0,0.18)';
      badge.style.color = 'var(--accent)';
      document.getElementById('kycSub').textContent = `${user.kyc.type} submitted · verification in progress`;
    }
  }

  function toggleEditPanel(id) {
    const panel = document.getElementById(id);
    const isOpen = panel.classList.contains('open');
    document.querySelectorAll('.edit-panel').forEach(p => p.classList.remove('open'));
    if (!isOpen) panel.classList.add('open');
  }

  function toggleAvatarPicker() {
    const p = document.getElementById('avatarPicker');
    p.style.display = p.style.display === 'none' ? 'block' : 'none';
  }

  function pickAvatar(emoji) {
    GKStore.setUser({ avatar: emoji });
    document.getElementById('userAvatarEmoji').textContent = emoji;
    const avatarBtn = document.getElementById('avatarBtn');
    if (avatarBtn) avatarBtn.innerHTML = emoji;
    document.getElementById('avatarPicker').style.display = 'none';
    showToast('Avatar updated! 🎉', 'success');
  }

  function saveName() {
    const val = document.getElementById('inputName').value.trim();
    if (!val) { showToast('Please enter your name', 'error'); return; }
    GKStore.setUser({ name: val });
    renderProfileData();
    toggleEditPanel('editName');
    showToast('Name updated successfully!', 'success');
  }

  function savePhone() {
    const val = document.getElementById('inputPhone').value.trim();
    if (!val) { showToast('Enter a valid phone number', 'error'); return; }
    GKStore.setUser({ phone: val });
    renderProfileData();
    toggleEditPanel('editPhone');
    showToast('Phone updated!', 'success');
  }

  function saveUPI() {
    const val = document.getElementById('inputUPI').value.trim();
    if (!val || !val.includes('@')) { showToast('Enter valid UPI ID (e.g. name@upi)', 'error'); return; }
    GKStore.setUser({ upi: val });
    renderProfileData();
    toggleEditPanel('editUPI');
    showToast('UPI ID saved!', 'success');
  }

  function submitKyc() {
    const type = document.getElementById('inputKycType').value;
    const number = document.getElementById('inputKycNumber').value.trim();
    if (!number) { showToast('Enter document number', 'error'); return; }
    GKStore.setUser({ kyc: { status: 'review', type, number } });
    renderProfileData();
    toggleEditPanel('editKyc');
    showToast('KYC submitted for review! 🪪', 'success');
  }

  function savePIN() {
    const p1 = document.getElementById('inputPin').value;
    const p2 = document.getElementById('inputConfirmPin').value;
    if (p1.length !== 4 || p1 !== p2) { showToast('4-digit PINs must match', 'error'); return; }
    GKStore.setUser({ pinSet: true });
    toggleEditPanel('editPin');
    showToast('Security PIN saved successfully! 🔢', 'success');
  }

  document.addEventListener('DOMContentLoaded', renderProfileData);
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
