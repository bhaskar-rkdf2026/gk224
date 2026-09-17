/* ==========================================================================
   GK224.COM - Earn Hub Engine (earn.js)
   Freelance gigs, Village student surveys, Stationery shop & Referrals
   ========================================================================== */

const FL_WHATSAPP = 'https://api.whatsapp.com/send?phone=919755578939&text=';
const freelanceProjects = [
  { id: 'fl1', company: 'Nirvana Retail Pvt Ltd', title: 'Shopify Product Page Redesign', desc: 'Redesign 12 product listing pages with faster load & mobile-first styling.', skills: ['Shopify', 'CSS', 'UI Design'], budget: 4500, deadline: '5 days', urgent: false },
  { id: 'fl2', company: 'Bright Path Academy', title: 'Landing Page for Admission Campaign', desc: 'Build a single conversion landing page with enquiry form & WhatsApp.', skills: ['HTML/CSS', 'JavaScript'], budget: 3200, deadline: '3 days', urgent: true },
  { id: 'fl3', company: 'Vertex Logistics', title: 'Delivery Tracking Dashboard UI', desc: 'Design and code admin dashboard UI to track live shipments & driver status.', skills: ['React', 'Dashboard UI'], budget: 8000, deadline: '10 days', urgent: false },
  { id: 'fl4', company: 'Studio Ember', title: 'Instagram Carousel Ad Set (10 posts)', desc: 'Design a 10-slide carousel ad set for a skincare brand product launch.', skills: ['Graphic Design', 'Canva/Figma'], budget: 2000, deadline: '2 days', urgent: true },
  { id: 'fl5', company: 'FinEdge Solutions', title: 'Excel to Web Report Automation', desc: 'Convert monthly Excel finance report into auto-generated web dashboard.', skills: ['Excel', 'JavaScript'], budget: 5500, deadline: '7 days', urgent: false },
  { id: 'fl6', company: 'Wanderlust Travels', title: 'Blog Content Writing (8 articles)', desc: 'Write 8 SEO-friendly travel blog articles, 800-1000 words each.', skills: ['Content Writing', 'SEO'], budget: 3600, deadline: '6 days', urgent: false }
];

let flActiveTab = 'open';

function openFreelance() {
  const modal = document.getElementById('freelanceModal');
  if (modal) modal.classList.add('open');
  renderFreelanceProjects();
}

function closeFreelance() {
  const modal = document.getElementById('freelanceModal');
  if (modal) modal.classList.remove('open');
}

function closeModalOutsideFreelance(e) {
  if (e.target === document.getElementById('freelanceModal')) closeFreelance();
}

function switchFlTab(tab) {
  flActiveTab = tab;
  const tabOpen = document.getElementById('flTabOpen');
  const tabMine = document.getElementById('flTabMine');
  if (tabOpen) tabOpen.classList.toggle('active', tab === 'open');
  if (tabMine) tabMine.classList.toggle('active', tab === 'mine');
  renderFreelanceProjects();
}

function renderFreelanceProjects() {
  const taken = GKStore.getFreelanceTaken();
  const listEl = document.getElementById('flProjectList');
  if (!listEl) return;

  const openProjects = freelanceProjects.filter(p => !taken.includes(p.id));
  const myProjects = freelanceProjects.filter(p => taken.includes(p.id));

  const openCountEl = document.getElementById('flOpenCount');
  const mineCountEl = document.getElementById('flMineCount');
  const earnedEl = document.getElementById('flEarnedCount');

  if (openCountEl) openCountEl.textContent = openProjects.length;
  if (mineCountEl) mineCountEl.textContent = myProjects.length;
  if (earnedEl) {
    const potential = openProjects.reduce((s, p) => s + p.budget, 0);
    earnedEl.textContent = `₹${potential.toLocaleString('en-IN')}`;
  }

  const list = flActiveTab === 'open' ? openProjects : myProjects;

  if (!list.length) {
    listEl.innerHTML = `
      <div class="fl-empty">
        <i class="ri-briefcase-4-line"></i>
        <p>${flActiveTab === 'open' ? 'No open projects right now — check back soon!' : "You haven't taken any projects yet."}</p>
      </div>`;
    return;
  }

  listEl.innerHTML = list.map(p => {
    const isMine = taken.includes(p.id);
    const badge = isMine
      ? '<span class="fl-project-badge fl-badge-mine">In Progress</span>'
      : (p.urgent ? '<span class="fl-project-badge fl-badge-urgent">Urgent</span>' : '<span class="fl-project-badge fl-badge-open">Open</span>');
    const btn = isMine
      ? `<button class="fl-take-btn taken" onclick="contactFlTeam('${p.id}')"><i class="ri-whatsapp-line"></i> Message Team</button>`
      : `<button class="fl-take-btn" onclick="takeFlProject('${p.id}')">Take Project</button>`;
    return `
      <div class="fl-project">
        <div class="fl-project-top">
          <div>
            <div class="fl-project-co"><i class="ri-building-line"></i> ${p.company}</div>
            <div class="fl-project-title">${p.title}</div>
          </div>
          ${badge}
        </div>
        <div class="fl-project-desc">${p.desc}</div>
        <div class="fl-skills">${p.skills.map(s => `<span class="fl-skill-chip">${s}</span>`).join('')}</div>
        <div class="fl-project-meta">
          <div class="fl-meta-left">
            <div class="fl-budget">₹${p.budget.toLocaleString('en-IN')}</div>
            <div class="fl-deadline"><i class="ri-time-line"></i> ${p.deadline} deadline</div>
          </div>
          ${btn}
        </div>
      </div>`;
  }).join('');
}

function takeFlProject(id) {
  const p = freelanceProjects.find(x => x.id === id);
  if (!p) return;
  GKStore.takeFreelanceProject(id);
  showToast(`Project taken! "${p.title}" is now yours 🎉`, 'success');
  addNotification('Freelance project assigned', `You picked up "${p.title}". Message our team on WhatsApp.`, 'ri-briefcase-4-line');
  renderFreelanceProjects();
  setTimeout(() => contactFlTeam(id), 500);
}

function contactFlTeam(id) {
  const p = freelanceProjects.find(x => x.id === id);
  if (!p) return;
  const msg = encodeURIComponent(`Hi GK224, I've taken up the freelance project "${p.title}" for ${p.company} (Budget ₹${p.budget}). Please share the client brief & files.`);
  window.open(FL_WHATSAPP + msg, '_blank');
}

// ─── VILLAGE STUDENT SURVEY ───────────────────────────
const SURVEY_REWARD = 10;

function openSurvey() {
  const modal = document.getElementById('surveyModal');
  if (modal) modal.classList.add('open');
  renderSurveyEntries();
  updateSurveyCounter();
}

function closeSurvey() {
  const modal = document.getElementById('surveyModal');
  if (modal) modal.classList.remove('open');
}

function closeModalOutsideSurvey(e) {
  if (e.target === document.getElementById('surveyModal')) closeSurvey();
}

function submitSurvey() {
  const name = document.getElementById('surveyName').value.trim();
  const age = document.getElementById('surveyAge').value.trim();
  const cls = document.getElementById('surveyClass').value.trim();
  const village = document.getElementById('surveyVillage').value.trim();
  const parent = document.getElementById('surveyParent').value.trim();
  const contact = document.getElementById('surveyContact').value.trim();
  const gender = document.getElementById('surveyGender').value;

  if (!name) { showToast('Enter student name', 'error'); return; }
  if (!age) { showToast('Enter student age', 'error'); return; }
  if (!village) { showToast('Enter village name', 'error'); return; }

  const entry = { name, age, cls, village, parent, contact, gender };
  GKStore.addSurveyEntry(entry);

  GKStore.addTransaction({
    type: 'earning',
    title: 'Survey Entry: ' + name,
    amount: SURVEY_REWARD,
    ico: '<i class="ri-survey-line"></i>'
  });

  renderSurveyEntries();
  updateSurveyCounter();

  ['surveyName','surveyAge','surveyClass','surveyVillage','surveyParent','surveyContact'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.value = '';
  });
  document.getElementById('surveyGender').value = '';

  showToast(`Entry saved! +₹${SURVEY_REWARD} added to wallet 🎉`, 'success');
  const count = GKStore.getSurveyEntries().length;
  if (count % 2 === 0 && typeof grantNewScratchCard === 'function') {
    setTimeout(() => grantNewScratchCard(`Survey Milestone #${count}`, 25, 'ri-survey-line'), 800);
  }
}

function updateSurveyCounter() {
  const list = GKStore.getSurveyEntries();
  const countEl = document.getElementById('surveyCount');
  const earnedEl = document.getElementById('surveyEarned');
  if (countEl) countEl.textContent = list.length;
  if (earnedEl) earnedEl.textContent = `₹${(list.length * SURVEY_REWARD).toLocaleString('en-IN')}`;
}

function renderSurveyEntries() {
  const el = document.getElementById('surveyEntriesList');
  if (!el) return;
  const list = GKStore.getSurveyEntries();
  if (!list.length) {
    el.innerHTML = '<div class="empty"><div class="empty-ico"><i class="ri-survey-line"></i></div><p>No entries yet</p></div>';
    return;
  }
  el.innerHTML = list.slice(0, 15).map(en => {
    const d = new Date(en.date);
    const dateStr = `${d.getDate()} ${d.toLocaleString('en-IN',{month:'short'})}`;
    return `
      <div class="survey-entry-item">
        <div class="survey-entry-ico"><i class="ri-user-smile-line"></i></div>
        <div>
          <div class="survey-entry-name">${en.name}${en.cls ? ' · ' + en.cls : ''}</div>
          <div class="survey-entry-meta">${en.village} · ${dateStr}</div>
        </div>
        <div class="survey-entry-amt">+₹${SURVEY_REWARD}</div>
      </div>`;
  }).join('');
}

// ─── STATIONERY STORE ─────────────────────────────────
function openStationery() {
  const modal = document.getElementById('stationeryModal');
  if (modal) modal.classList.add('open');
}

function closeStationery() {
  const modal = document.getElementById('stationeryModal');
  if (modal) modal.classList.remove('open');
}

function closeModalOutsideStationery(e) {
  if (e.target === document.getElementById('stationeryModal')) closeStationery();
}

function buyStationery(name, price, ico) {
  const currentBal = GKStore.getBalance();
  if (currentBal < price) { showToast('Insufficient wallet balance', 'error'); return; }

  GKStore.addTransaction({
    type: 'payment',
    title: 'Bought ' + name,
    amount: -price,
    category: 'shopping',
    ico: ico
  });

  autoCashback(price, 'shopping', name);
  showToast(`Purchased ${name}! 🛍️`, 'success');
}

// ─── REFERRAL SYSTEM ──────────────────────────────────
const MY_CODE = 'GK224' + Math.random().toString(36).substr(2,4).toUpperCase();
const LEADERBOARD_DEMO = [
  { name: 'Priya S.', points: 14 },
  { name: 'Rohan K.', points: 11 },
  { name: 'Ananya M.', points: 8 },
  { name: 'Vikram J.', points: 5 }
];

function openReferral() {
  updateRefStats();
  renderLeaderboard();
  const codeEl = document.getElementById('refCodeDisplay');
  if (codeEl) codeEl.textContent = MY_CODE;
  const modal = document.getElementById('referralModal');
  if (modal) modal.classList.add('open');
}

function closeReferral() {
  const modal = document.getElementById('referralModal');
  if (modal) modal.classList.remove('open');
}

function closeModalOutsideReferral(e) {
  if (e.target === document.getElementById('referralModal')) closeReferral();
}

function copyCode() {
  const url = `${window.location.origin}/login.php?ref=${MY_CODE}`;
  navigator.clipboard.writeText(url).then(() => {
    const btn = document.getElementById('copyBtn');
    if (btn) btn.innerHTML = '<i class="ri-check-line"></i> Copied!';
    setTimeout(() => {
      if (btn) btn.innerHTML = '<i class="ri-clipboard-line"></i> Copy';
    }, 2000);
    showToast('Referral link copied!', 'success');
  }).catch(() => {
    showToast(`Code: ${MY_CODE}`, 'success');
  });
}

function shareReferral() {
  const url = `${window.location.origin}/login.php?ref=${MY_CODE}`;
  const text = `✈️ Join GK224.COM App!\nSign up using my referral code: ${MY_CODE}\nHelp me win a free trip — most referrals this month wins!\n${url}`;
  if (navigator.share) {
    navigator.share({ title: 'GK224.COM — Refer & Win a Free Trip', text, url }).catch(() => {});
  } else {
    navigator.clipboard.writeText(text).then(() => showToast('Share text copied!', 'success'));
  }
}

const FRIEND_NAMES = ['Rahul','Priya','Amit','Sneha','Vikram','Pooja','Arjun','Kavya','Rohan','Ananya'];
let nameIdx = 0;

function simulateFriendJoin() {
  const name = FRIEND_NAMES[nameIdx % FRIEND_NAMES.length];
  nameIdx++;
  const pts = GKStore.isProUser() ? 2 : 1;
  const refData = GKStore.getReferralData();

  refData.count++;
  refData.points += pts;
  refData.friends.unshift({ name, date: new Date().toISOString(), pts });
  GKStore.saveReferralData(refData);

  updateRefStats();
  renderLeaderboard();

  const histEl = document.getElementById('refHistory');
  if (histEl) histEl.style.display = 'block';
  renderFriendsList();

  showToast(`${name} joined! +${pts} point${pts > 1 ? 's' : ''} 🏆`, 'success');
  if (typeof grantNewScratchCard === 'function') {
    setTimeout(() => grantNewScratchCard(`Referral: ${name} Joined`, 30, 'ri-gift-line'), 1000);
  }
}

function updateRefStats() {
  const refData = GKStore.getReferralData();
  const countEl = document.getElementById('refCount');
  const earnedEl = document.getElementById('friendsEarned');
  const rankEl = document.getElementById('refEarned');
  if (countEl) countEl.textContent = refData.count;
  if (earnedEl) earnedEl.textContent = refData.points;
  if (rankEl) {
    const combined = LEADERBOARD_DEMO.map(p => p.points).concat(refData.points);
    const sorted = [...combined].sort((a, b) => b - a);
    const rank = sorted.indexOf(refData.points) + 1;
    rankEl.textContent = `#${rank}`;
  }
}

function renderLeaderboard() {
  const listEl = document.getElementById('leaderboardList');
  if (!listEl) return;
  const refData = GKStore.getReferralData();
  const you = { name: 'You', points: refData.points, isYou: true };
  const combined = [...LEADERBOARD_DEMO, you].sort((a, b) => b.points - a.points);

  listEl.innerHTML = combined.map((p, i) => {
    const medal = i === 0 ? '🥇' : i === 1 ? '🥈' : i === 2 ? '🥉' : `${i + 1}.`;
    return `
      <div class="ref-friend" style="${p.isYou ? 'border:1px solid rgba(181,212,0,0.35);background:linear-gradient(155deg, rgba(181,212,0,0.06), var(--surface));' : ''}">
        <div class="ref-friend-ico">${medal}</div>
        <div>
          <div class="ref-friend-name">${p.name}${p.isYou ? ' (You)' : ''}</div>
        </div>
        <div class="ref-friend-bonus">${p.points} pts</div>
      </div>`;
  }).join('');
}

function renderFriendsList() {
  const el = document.getElementById('refFriendsList');
  if (!el) return;
  const refData = GKStore.getReferralData();
  el.innerHTML = refData.friends.slice(0, 5).map(f => {
    const d = new Date(f.date);
    return `
      <div class="ref-friend">
        <div class="ref-friend-ico"><i class="ri-user-3-fill"></i></div>
        <div>
          <div class="ref-friend-name">${f.name}</div>
          <div class="ref-friend-date">${d.getDate()} ${d.toLocaleString('en-IN',{month:'short'})}</div>
        </div>
        <div class="ref-friend-bonus">+${f.pts} pt${f.pts > 1 ? 's' : ''}</div>
      </div>`;
  }).join('');
}
