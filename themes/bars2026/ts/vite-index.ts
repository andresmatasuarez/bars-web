// Main entry point for Vite build
// Import Tailwind CSS
import '../css/main.css';

// Import components
import { initMobileMenu } from './components/mobile-menu';

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
  initMobileMenu();
});
