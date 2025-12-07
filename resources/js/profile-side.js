

document.addEventListener('alpine:init', () => {
  Alpine.data('profileTabs', () => ({
    tab: 'info',
    valid: [],
    init() {
      try {
        this.valid = Array.from(this.$root.querySelectorAll('section [id]')).map(el => el.id);
      } catch {}
      this.syncFromHash();
      window.addEventListener('popstate', () => this.syncFromHash());
      this.$watch('tab', v => this.syncHash(v));
    },
    syncFromHash() {
      const h = (location.hash || '').replace('#','');
      if (this.valid.includes(h)) this.tab = h; else if (this.valid.length) this.tab = this.valid[0];
    },
    syncHash(v) {
      if (!this.valid.includes(v)) return;
      const url = `${location.pathname}${location.search}#${v}`;
      history.replaceState({}, '', url);
    },
    setTab(k) { if (this.valid.includes(k)) this.tab = k; }
  }));
});

