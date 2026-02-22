import { useCallback, useState } from 'react';

import { ShareIcon } from './icons';

type Props = {
  url: string;
  title?: string;
  size?: 'sm' | 'md';
  tooltipPosition?: 'above' | 'below';
  tooltipAlign?: 'center' | 'right';
  className?: string;
};

export default function ShareButton({ url, title, size = 'sm', tooltipPosition = 'above', tooltipAlign = 'center', className }: Props) {
  const [showTooltip, setShowTooltip] = useState(false);

  const sizeClasses = size === 'md' ? 'w-10 h-10' : 'w-8 h-8';
  const iconSize = size === 'md' ? 20 : 16;

  const handleClick = useCallback(async () => {
    if (navigator.share) {
      try {
        await navigator.share({ url, title });
        return;
      } catch {
        // User cancelled or share failed — fall through to clipboard
      }
    }

    try {
      await navigator.clipboard.writeText(url);
    } catch {
      // Clipboard API unavailable — ignore silently
      return;
    }

    setShowTooltip(true);
    setTimeout(() => setShowTooltip(false), 2000);
  }, [url, title]);

  return (
    <button
      type="button"
      onClick={(e) => {
        e.stopPropagation();
        e.preventDefault();
        handleClick();
      }}
      className={`relative ${sizeClasses} shrink-0 rounded-full flex items-center justify-center transition-all cursor-pointer active:scale-90 bg-black/40 text-white/70 hover:bg-black/60${className ? ` ${className}` : ''}`}
      aria-label="Compartir"
      title="Compartir"
    >
      <ShareIcon size={iconSize} />
      {showTooltip && (
        <span
          className={`absolute whitespace-nowrap rounded bg-black/80 px-2 py-1 text-[11px] text-white pointer-events-none animate-fade-in ${
            tooltipAlign === 'right' ? 'right-0' : 'left-1/2 -translate-x-1/2'
          } ${tooltipPosition === 'above' ? 'bottom-full mb-1.5' : 'top-full mt-1.5'}`}
        >
          Enlace copiado
        </span>
      )}
    </button>
  );
}
