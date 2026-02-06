/**
 * About page tab switching
 */
export function initAboutTabs(): void {
  const tabs = document.querySelectorAll<HTMLButtonElement>('.about-tab');
  const panels = document.querySelectorAll<HTMLElement>('.about-tab-panel');

  if (!tabs.length || !panels.length) return;

  tabs.forEach((tab) => {
    tab.addEventListener('click', () => {
      const target = tab.dataset.tab;
      if (!target) return;

      // Update tab buttons
      tabs.forEach((t) => {
        const isActive = t.dataset.tab === target;
        t.setAttribute('aria-selected', String(isActive));
        if (isActive) {
          t.classList.remove('bg-bars-bg-medium', 'text-bars-text-muted', 'font-medium', 'hover:text-white');
          t.classList.add('bg-bars-primary', 'text-white', 'font-semibold', 'hover:bg-[#A00000]');
        } else {
          t.classList.remove('bg-bars-primary', 'text-white', 'font-semibold', 'hover:bg-[#A00000]');
          t.classList.add('bg-bars-bg-medium', 'text-bars-text-muted', 'font-medium', 'hover:text-white');
        }
      });

      // Show/hide panels
      panels.forEach((panel) => {
        const panelId = panel.id.replace('tab-', '');
        panel.classList.toggle('hidden', panelId !== target);
      });
    });
  });
}
