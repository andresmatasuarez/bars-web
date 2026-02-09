// Main entry point for Vite build
// Import Tailwind CSS
import '../css/main.css';

// Import components
import { initMobileMenu } from './components/mobile-menu';
import { initAboutTabs } from './components/about-tabs';
import { initJuryModal } from './components/jury-modal/JuryModal';

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
  initMobileMenu();
  initAboutTabs();
  initJuryModal();
});
