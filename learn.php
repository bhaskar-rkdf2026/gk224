<?php
$pageTitle = 'GK224.COM — Learning Hub';
$activePage = 'learn';
$showBalanceCard = false;
include __DIR__ . '/includes/header.php';
?>

<!-- ===== LEARN SECTION ===== -->
<section id="learn" class="section active">
  <div class="sec-head">
    <div class="sec-title">Learning Hub</div>
    <div class="sec-sub">Upskill &amp; grow your career with AI</div>
  </div>

  <!-- VILLAGE IMPACT TRACKER -->
  <div class="village-impact-card">
    <div class="vic-head"><i class="ri-community-line"></i> Your Village Impact (Shivpuri Hub)</div>
    <div class="vic-stats">
      <div class="vic-stat"><div class="vic-num" id="vicLearning">45</div><div class="vic-lbl">Learning</div></div>
      <div class="vic-stat"><div class="vic-num" id="vicCompleted">128</div><div class="vic-lbl">Completed</div></div>
      <div class="vic-stat"><div class="vic-num" id="vicStreak">1</div><div class="vic-lbl">Daily Streak 🔥</div></div>
    </div>
  </div>

  <!-- SUCCESS STORIES -->
  <div class="success-stories">
    <div class="ss-title"><i class="ri-star-smile-line"></i> Success Stories</div>
    <div class="ss-row">
      <div class="ss-card">
        <div class="ss-avatar">👨‍🎓</div>
        <div class="ss-name">Ramesh, Shivpuri</div>
        <div class="ss-text">Completed Digital Marketing &amp; now manages social media for 3 local shops.</div>
      </div>
      <div class="ss-card">
        <div class="ss-avatar">👩‍🎓</div>
        <div class="ss-name">Kavya, Shivpuri</div>
        <div class="ss-text">Learned AI Website Building and started freelancing part-time with college.</div>
      </div>
      <div class="ss-card">
        <div class="ss-avatar">🧑‍🎓</div>
        <div class="ss-name">Arjun, Shivpuri</div>
        <div class="ss-text">Used the AI Presentation course to win inter-college presentation awards.</div>
      </div>
    </div>
  </div>

  <!-- STATS ROW & LIVE CLASS -->
  <div class="stats-row">
    <div class="stat-card">
      <div class="stat-num" id="coursesCompleted">0</div>
      <div class="stat-lbl">Enrolled</div>
    </div>
    <div class="stat-card">
      <div class="stat-num" id="skillsLearned">0</div>
      <div class="stat-lbl">Skills</div>
    </div>
    <div class="stat-card" style="cursor:pointer" onclick="window.open('https://meet.google.com/nmx-uqbn-ghi','_blank')">
      <div class="stat-num"><i class="ri-group-line" style="color:var(--accent)"></i></div>
      <div class="stat-lbl">Join Live Class</div>
    </div>
  </div>

  <!-- COURSE 1 -->
  <div class="course-card">
    <div class="course-badge badge-fin">AI &amp; Presentation</div>
    <div class="course-title">AI Presentation Mastery</div>
    <div class="course-desc">Create jaw-dropping slide decks in minutes using generative AI prompts &amp; designs.</div>
    <div class="course-foot">
      <div class="course-price">₹999</div>
      <button type="button" class="btn-sm" onclick="openEnrollModal('AI Presentation Mastery', 999, 'AI &amp; Presentation', 'Create jaw-dropping slide decks in minutes using generative AI prompts.')">Enroll Now →</button>
    </div>
  </div>

  <!-- COURSE 2 -->
  <div class="course-card">
    <div class="course-badge badge-mkt">Web &amp; Development</div>
    <div class="course-title">AI Website Builder</div>
    <div class="course-desc">Master modern responsive web development, SEO, and fast site deployment.</div>
    <div class="course-foot">
      <div class="course-price">₹1,499</div>
      <button type="button" class="btn-sm" onclick="openEnrollModal('AI Website Builder', 1499, 'Web &amp; Development', 'Master modern responsive web development, SEO, and fast site deployment.')">Enroll Now →</button>
    </div>
  </div>

  <!-- COURSE 3 -->
  <div class="course-card">
    <div class="course-badge badge-photo">Marketing &amp; Growth</div>
    <div class="course-title">AI Digital Marketing Pro</div>
    <div class="course-desc">Scale brand reach, run targeted ad campaigns, and drive conversions with AI tools.</div>
    <div class="course-foot">
      <div class="course-price">₹799</div>
      <button type="button" class="btn-sm" onclick="openEnrollModal('AI Digital Marketing Pro', 799, 'Marketing &amp; Growth', 'Scale brand reach, run targeted ad campaigns, and drive conversions.')">Enroll Now →</button>
    </div>
  </div>
</section>

<!-- ═══ CERTIFICATE MODAL ═══ -->
<div class="modal-overlay" id="certificateModal" onclick="closeModalOutsideCert(event)">
  <div class="modal-sheet" onclick="event.stopPropagation()">
    <div class="modal-handle"></div>
    <button class="back-btn" onclick="closeCertificate()" title="Back"><i class="ri-arrow-left-line"></i></button>
    <div class="modal-title"><i class="ri-award-line"></i> Certificate of Completion</div>
    <div class="cert-preview">
      <div class="cert-badge">🏅</div>
      <div class="cert-line1">This certifies that</div>
      <div class="cert-name" id="certName">Student</div>
      <div class="cert-line2">has successfully completed</div>
      <div class="cert-course" id="certCourse">Course Title</div>
      <div class="cert-date" id="certDate">Date</div>
      <div class="cert-brand">GK224.COM LEARNING HUB</div>
    </div>
    <button class="btn btn-primary" onclick="downloadCertificate()"><i class="ri-download-2-line"></i> Download Certificate (PNG)</button>
  </div>
</div>

<script>
  function openEnrollModal(title, price, category, desc) {
    const bal = GKStore.getBalance();
    if (price > bal) {
      showToast('Insufficient wallet balance to enroll', 'error');
      return;
    }
    openConfirmModal({
      icon: 'ri-book-2-line',
      title: 'Enroll in Course',
      sub: 'Review course details before purchase',
      itemIcon: '<i class="ri-book-open-line"></i>',
      itemTitle: title,
      itemDesc: desc,
      priceLabel: 'Course Fee',
      price: `₹${price.toLocaleString('en-IN')}`,
      confirmLabel: `Pay & Enroll ₹${price.toLocaleString('en-IN')}`,
      onConfirm: function() {
        GKStore.addTransaction({
          type: 'learning',
          title: 'Enrolled: ' + title,
          amount: -price,
          category: 'other',
          ico: '<i class="ri-book-open-line"></i>'
        });
        showToast(`Enrolled in ${title}! 🎉`, 'success');
        addNotification('Course Enrollment Successful! 🎓', `You are enrolled in "${title}". Access live classes anytime!`, 'ri-book-open-line');
        showCertificateModal(title);
      }
    });
  }

  function showCertificateModal(courseTitle) {
    const user = GKStore.getUser();
    document.getElementById('certName').textContent = user.name || 'Student';
    document.getElementById('certCourse').textContent = courseTitle;
    document.getElementById('certDate').textContent = new Date().toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
    document.getElementById('certificateModal').classList.add('open');
  }

  function closeCertificate() {
    document.getElementById('certificateModal').classList.remove('open');
  }

  function closeModalOutsideCert(e) {
    if (e.target === document.getElementById('certificateModal')) closeCertificate();
  }

  function downloadCertificate() {
    const canvas = document.createElement('canvas');
    canvas.width = 900;
    canvas.height = 620;
    const ctx = canvas.getContext('2d');

    ctx.fillStyle = '#16161e';
    ctx.fillRect(0, 0, 900, 620);

    ctx.strokeStyle = '#b5d400';
    ctx.lineWidth = 6;
    ctx.strokeRect(24, 24, 852, 572);

    ctx.textAlign = 'center';
    ctx.fillStyle = '#b5d400';
    ctx.font = 'bold 32px Georgia';
    ctx.fillText('CERTIFICATE OF COMPLETION', 450, 150);

    ctx.fillStyle = '#f0f0f5';
    ctx.font = '18px Georgia';
    ctx.fillText('This certifies that', 450, 220);

    ctx.fillStyle = '#ffffff';
    ctx.font = 'bold 36px Georgia';
    ctx.fillText(document.getElementById('certName').textContent, 450, 280);

    ctx.fillStyle = '#f0f0f5';
    ctx.font = '18px Georgia';
    ctx.fillText('has successfully completed the course', 450, 340);

    ctx.fillStyle = '#b5d400';
    ctx.font = 'bold 28px Georgia';
    ctx.fillText(document.getElementById('certCourse').textContent, 450, 390);

    ctx.fillStyle = '#a8a8b0';
    ctx.font = '16px Georgia';
    ctx.fillText('Issued on ' + document.getElementById('certDate').textContent, 450, 470);

    ctx.fillStyle = '#b5d400';
    ctx.font = 'bold 20px Georgia';
    ctx.fillText('GK224.COM LEARNING HUB', 450, 550);

    const link = document.createElement('a');
    link.download = 'GK224-Certificate.png';
    link.href = canvas.toDataURL('image/png');
    link.click();
    showToast('Certificate downloaded! 📥', 'success');
  }

  document.addEventListener('DOMContentLoaded', function() {
    const txs = GKStore.getTransactions();
    const enrolled = txs.filter(t => t.type === 'learning');
    document.getElementById('coursesCompleted').textContent = enrolled.length;
    document.getElementById('skillsLearned').textContent = enrolled.length * 3;
    document.getElementById('vicStreak').textContent = GKStore.updateStreak();
  });
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
