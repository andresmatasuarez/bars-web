import { useCallback, useMemo, useState } from 'react';

import { SHARE_PARAM } from '../data/shareableList';
import { XIcon } from './icons';

function isInAppWebView(): boolean {
  const ua = navigator.userAgent || '';
  // Facebook
  if (/FBAN|FBAV/i.test(ua)) return true;
  // Instagram
  if (/Instagram/i.test(ua)) return true;
  // Twitter/X
  if (/Twitter/i.test(ua)) return true;
  // Line
  if (/\bLine\//i.test(ua)) return true;
  // Android WebView
  if (/; wv\b/.test(ua)) return true;
  // WhatsApp (sometimes opens in an in-app browser)
  if (/WhatsApp/i.test(ua)) return true;
  return false;
}

function hasShareParam(): boolean {
  return new URLSearchParams(window.location.search).has(SHARE_PARAM);
}

/** Fallback for non-secure contexts. */
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
    // not supported
  }
  document.body.removeChild(ta);
  return ok;
}

export default function WebViewBanner() {
  const [dismissed, setDismissed] = useState(false);
  const [copied, setCopied] = useState(false);

  const shouldShow = useMemo(() => isInAppWebView() && hasShareParam(), []);

  const handleCopy = useCallback(async () => {
    const url = window.location.href;
    let ok = false;

    if (window.isSecureContext && navigator.clipboard) {
      try {
        await navigator.clipboard.writeText(url);
        ok = true;
      } catch {
        // fall through
      }
    }
    if (!ok) ok = legacyCopy(url);

    if (ok) {
      setCopied(true);
      setTimeout(() => setCopied(false), 2000);
    }
  }, []);

  if (!shouldShow || dismissed) return null;

  return (
    <div className="relative bg-amber-900/40 border border-amber-500/30 rounded-bars-md px-4 py-3 mb-4">
      <button
        type="button"
        onClick={() => setDismissed(true)}
        className="absolute top-2 right-2 text-amber-300/60 hover:text-amber-200 cursor-pointer"
      >
        <XIcon size={14} />
      </button>
      <p className="text-sm text-amber-200 pr-6">
        Para que esta lista se guarde en tu navegador, abrí este enlace en Safari o Chrome.
      </p>
      <button
        type="button"
        onClick={handleCopy}
        className="mt-2 inline-flex items-center gap-1.5 rounded-bars-pill px-3 py-1.5 text-xs font-medium bg-amber-500/20 border border-amber-500/40 text-amber-200 hover:bg-amber-500/30 transition-colors cursor-pointer"
      >
        {copied ? 'Enlace copiado!' : 'Copiar enlace'}
      </button>
    </div>
  );
}
