// Main entry point for Vite build
// Import Tailwind CSS
import '../css/main.css';

// Import components
import { initAboutTabs } from './components/about-tabs';
import { initJuryModal } from './components/jury-modal/JuryModal';
import { initMobileMenu } from './components/mobile-menu';

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
  initMobileMenu();
  initAboutTabs();
  initJuryModal();
});
