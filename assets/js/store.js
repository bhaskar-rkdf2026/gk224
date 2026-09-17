/* ==========================================================================
   GK224.COM - Persistent State Store (store.js)
   Synchronizes Balance, Transactions, User Data, Scratchcards, Surveys,
   and GK Pro status across all PHP pages via localStorage.
   ========================================================================== */

const GKStore = (function() {
  const DEFAULT_STATE = {
    balance: 10000,
    transactions: [],
    currentUser: {
      name: 'John Doe',
      username: '@johndoe',
      email: 'john@example.com',
      phone: '+91 98765 43210',
      upi: '',
      avatar: '👤',
      since: 'June 2025',
      kyc: { status: 'pending', type: '', number: '' },
      pinSet: false,
      language: 'English'
    },
    scratchCards: [
      { id: 'sc_welcome', title: 'Daily Login Reward', desc: 'Tap to scratch & win cash', reward: 15, status: 'unscratched', icon: 'ri-gift-2-line' },
      { id: 'sc_mystery', title: 'Welcome Mystery Card', desc: 'Special signup lucky reward', reward: 35, status: 'unscratched', icon: 'ri-sparkling-2-line' },
      { id: 'sc_task', title: 'Shivpuri Super Bonus', desc: 'Active student reward', reward: 25, status: 'unscratched', icon: 'ri-vip-diamond-line' }
    ],
    surveyEntries: [],
    freelanceTaken: [],
    referralData: { count: 0, points: 0, friends: [] },
    isPro: false,
    streak: 1,
    notifications: []
  };

  function getStorage(key, fallback) {
    try {
      const val = localStorage.getItem('gk_' + key);
      return val ? JSON.parse(val) : fallback;
    } catch(e) {
      return fallback;
    }
  }

  function setStorage(key, value) {
    try {
      localStorage.setItem('gk_' + key, JSON.stringify(value));
    } catch(e) {}
  }

  return {
    // ── BALANCE ──
    getBalance: function() {
      return getStorage('balance', DEFAULT_STATE.balance);
    },
    setBalance: function(amt) {
      setStorage('balance', amt);
      this.notifyStateChange();
    },

    // ── TRANSACTIONS ──
    getTransactions: function() {
      return getStorage('transactions', DEFAULT_STATE.transactions);
    },
    addTransaction: function(tx) {
      const list = this.getTransactions();
      tx.id = Date.now();
      tx.date = new Date().toISOString();
      list.unshift(tx);
      setStorage('transactions', list);
      
      const newBal = this.getBalance() + tx.amount;
      this.setBalance(newBal);
      return tx;
    },

    // ── CURRENT USER ──
    getUser: function() {
      return getStorage('user', DEFAULT_STATE.currentUser);
    },
    setUser: function(userData) {
      const current = this.getUser();
      const updated = Object.assign({}, current, userData);
      setStorage('user', updated);
      this.notifyStateChange();
      return updated;
    },

    // ── SCRATCHPAD CARDS ──
    getScratchCards: function() {
      return getStorage('scratch_cards_v2', DEFAULT_STATE.scratchCards);
    },
    saveScratchCards: function(cards) {
      setStorage('scratch_cards_v2', cards);
    },
    addScratchCard: function(card) {
      const list = this.getScratchCards();
      list.unshift(card);
      this.saveScratchCards(list);
    },

    // ── SURVEY ENTRIES ──
    getSurveyEntries: function() {
      return getStorage('surveys', DEFAULT_STATE.surveyEntries);
    },
    addSurveyEntry: function(entry) {
      const list = this.getSurveyEntries();
      entry.date = new Date().toISOString();
      list.unshift(entry);
      setStorage('surveys', list);
      return entry;
    },

    // ── FREELANCE GIGS ──
    getFreelanceTaken: function() {
      return getStorage('fl_taken', DEFAULT_STATE.freelanceTaken);
    },
    takeFreelanceProject: function(id) {
      const taken = this.getFreelanceTaken();
      if (!taken.includes(id)) {
        taken.push(id);
        setStorage('fl_taken', taken);
      }
    },

    // ── REFERRAL DATA ──
    getReferralData: function() {
      return getStorage('referral_data', DEFAULT_STATE.referralData);
    },
    saveReferralData: function(data) {
      setStorage('referral_data', data);
    },

    // ── GK PRO ──
    isProUser: function() {
      return localStorage.getItem('gk_pro') === '1';
    },
    setProUser: function(planKey) {
      localStorage.setItem('gk_pro', '1');
      localStorage.setItem('gk_pro_plan', planKey);
      this.notifyStateChange();
    },

    // ── NOTIFICATIONS ──
    getNotifications: function() {
      return getStorage('notifications', DEFAULT_STATE.notifications);
    },
    saveNotifications: function(list) {
      setStorage('notifications', list);
    },

    // ── STREAK ──
    updateStreak: function() {
      const today = new Date().toDateString();
      const last = localStorage.getItem('gk_last_visit');
      let streak = parseInt(localStorage.getItem('gk_streak') || '1', 10);

      if (last !== today) {
        const yesterday = new Date(Date.now() - 86400000).toDateString();
        streak = (last === yesterday) ? streak + 1 : 1;
        localStorage.setItem('gk_last_visit', today);
        localStorage.setItem('gk_streak', String(streak));
      }
      return streak;
    },

    notifyStateChange: function() {
      window.dispatchEvent(new Event('gkStateChanged'));
    }
  };
})();
