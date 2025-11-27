import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

import './wilayah';
import './loading-handler';
import './cart';
import './pagination'
import './partials/checkout-payment';

// Global Alpine store for layout (mobile sidebar)
document.addEventListener('alpine:init', () => {
  const persisted = (() => {
    try { return JSON.parse(localStorage.getItem('sidebarCollapsed') || 'false'); } catch { return false }
  })();

  Alpine.store('layout', {
    sidebarOpen: false,
    sidebarCollapsed: persisted,
    toggleSidebar() { this.sidebarOpen = !this.sidebarOpen },
    toggleSidebarCollapse() {
      this.sidebarCollapsed = !this.sidebarCollapsed;
      try { localStorage.setItem('sidebarCollapsed', JSON.stringify(this.sidebarCollapsed)); } catch {}
    },
    closeSidebar() { this.sidebarOpen = false },
  })
})
