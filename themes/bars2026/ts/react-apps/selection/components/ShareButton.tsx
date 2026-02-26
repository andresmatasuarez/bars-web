import { useCallback, useRef, useState } from 'react';

import { ShareIcon } from './icons';

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

/** Copy text to clipboard, trying Clipboard API first with legacy fallback. */
export async function copyToClipboard(text: string): Promise<boolean> {
  if (window.isSecureContext && navigator.clipboard) {
    try {
      await navigator.clipboard.writeText(text);
      return true;
    } catch {
      // Clipboard API failed — fall through to legacy
    }
  }
  return legacyCopy(text);
}

/** Hook encapsulating share/copy flow with tooltip state. */
export function useShareCopy() {
  const [showTooltip, setShowTooltip] = useState(false);
  const timeoutRef = useRef<ReturnType<typeof setTimeout> | undefined>(undefined);

  const share = useCallback(async (url: string, title?: string) => {
    if (navigator.share) {
      try {
        await navigator.share({ url, title });
        return;
      } catch {
        // User cancelled or share failed — fall through to clipboard
      }
    }

    const copied = await copyToClipboard(url);
    if (!copied) return;

    clearTimeout(timeoutRef.current);
    setShowTooltip(true);
    timeoutRef.current = setTimeout(() => setShowTooltip(false), 2000);
  }, []);

  return { share, showTooltip };
}

type CopiedTooltipProps = {
  show: boolean;
  position?: 'above' | 'below';
  align?: 'center' | 'right';
};

export function CopiedTooltip({ show, position = 'above', align = 'center' }: CopiedTooltipProps) {
  if (!show) return null;
  return (
    <span
      className={`absolute whitespace-nowrap rounded bg-black/80 px-2 py-1 text-[11px] text-white pointer-events-none animate-fade-in ${
        align === 'right' ? 'right-0' : 'left-1/2 -translate-x-1/2'
      } ${position === 'above' ? 'bottom-full mb-1.5' : 'top-full mt-1.5'}`}
    >
      Enlace copiado
    </span>
  );
}

type Props = {
  url: string;
  title?: string;
  size?: 'sm' | 'md';
  tooltipPosition?: 'above' | 'below';
  tooltipAlign?: 'center' | 'right';
  className?: string;
};

export default function ShareButton({
  url,
  title,
  size = 'sm',
  tooltipPosition = 'above',
  tooltipAlign = 'center',
  className,
}: Props) {
  const { share, showTooltip } = useShareCopy();

  const sizeClasses = size === 'md' ? 'w-10 h-10' : 'w-8 h-8';
  const iconSize = size === 'md' ? 20 : 16;

  return (
    <button
      type="button"
      onClick={(e) => {
        e.stopPropagation();
        e.preventDefault();
        share(url, title);
      }}
      className={`relative ${sizeClasses} shrink-0 rounded-full flex items-center justify-center transition-all cursor-pointer active:scale-90 bg-black/40 text-white/70 hover:bg-black/60${className ? ` ${className}` : ''}`}
      aria-label="Compartir"
      title="Compartir"
    >
      <ShareIcon size={iconSize} />
      <CopiedTooltip show={showTooltip} position={tooltipPosition} align={tooltipAlign} />
    </button>
  );
}
