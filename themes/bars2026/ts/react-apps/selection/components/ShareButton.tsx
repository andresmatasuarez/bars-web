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

/** Fallback for non-secure contexts where navigator.clipboard is unavailable. */
function legacyCopy(text: string): boolean {
  const ta = document.createElement('textarea');
  ta.value = text;
  ta.setAttribute('readonly', '');
  ta.style.position = 'fixed';
  ta.style.left = '-9999px';
  document.body.appendChild(ta);
  ta.select();
  let ok = false;
  try {
    ok = document.execCommand('copy');
  } catch {
    // execCommand not supported
  }
  document.body.removeChild(ta);
  return ok;
}

export default function ShareButton({ url, title, size = 'sm', tooltipPosition = 'above', tooltipAlign = 'center', className }: Props) {
  const [showTooltip, setShowTooltip] = useState(false);

  const sizeClasses = size === 'md' ? 'w-10 h-10' : 'w-8 h-8';
  const iconSize = size === 'md' ? 20 : 16;

  const handleClick = useCallback(async () => {
    // 1. Native share sheet (mobile)
    if (navigator.share) {
      try {
        await navigator.share({ url, title });
        return;
      } catch {
        // User cancelled or share failed — fall through to clipboard
      }
    }

    // 2. Clipboard API (secure contexts only)
    let copied = false;
    if (window.isSecureContext && navigator.clipboard) {
      try {
        await navigator.clipboard.writeText(url);
        copied = true;
      } catch {
        // Clipboard API failed — fall through to legacy
      }
    }

    // 3. Legacy execCommand fallback (works on HTTP)
    if (!copied) {
      copied = legacyCopy(url);
    }

    if (!copied) return;

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
