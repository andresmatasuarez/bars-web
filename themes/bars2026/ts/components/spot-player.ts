export function initSpotPlayer(): void {
  document.querySelectorAll<HTMLElement>('.spot-video-container').forEach((container) => {
    container.addEventListener('click', () => {
      const videoId = container.dataset.videoId;
      if (!videoId) return;

      const iframe = document.createElement('iframe');
      iframe.src = `https://www.youtube-nocookie.com/embed/${videoId}?autoplay=1&rel=0`;
      iframe.className = 'w-full h-full';
      iframe.allow = 'autoplay; encrypted-media';
      iframe.allowFullscreen = true;
      iframe.setAttribute('frameborder', '0');

      container.innerHTML = '';
      container.appendChild(iframe);
      container.classList.remove('cursor-pointer', 'group');
    });
  });
}
