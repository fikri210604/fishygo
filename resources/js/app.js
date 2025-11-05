import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

import './wilayah';
import './loading-buttons';

// Global Alpine store for layout (mobile sidebar)
document.addEventListener('alpine:init', () => {
  Alpine.store('layout', {
    sidebarOpen: false,
    toggleSidebar() { this.sidebarOpen = !this.sidebarOpen },
    closeSidebar() { this.sidebarOpen = false },
  })
})

// SweetAlert2 removed in favor of DaisyUI toast
