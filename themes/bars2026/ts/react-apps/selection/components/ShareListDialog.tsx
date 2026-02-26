import { WatchlistEntry } from '@shared/ts/useWatchlist';
import { useCallback, useState } from 'react';

import { Modal } from '../../../components/modal/Modal';
import { buildShareUrl, MAX_LIST_NAME } from '../data/shareableList';
import { ShareIcon, XIcon } from './icons';
import { copyToClipboard } from './ShareButton';

interface ShareListDialogProps {
  isOpen: boolean;
  onClose: () => void;
  watchlist: WatchlistEntry[];
}

export default function ShareListDialog({ isOpen, onClose, watchlist }: ShareListDialogProps) {
  const [name, setName] = useState('');
  const [shared, setShared] = useState(false);

  const trimmedName = name.trim();
  const canShare = trimmedName.length > 0 && watchlist.length > 0;

  const handleClose = useCallback(() => {
    setName('');
    setShared(false);
    onClose();
  }, [onClose]);

  const handleShare = useCallback(async () => {
    if (!canShare) return;

    const url = buildShareUrl(trimmedName, watchlist);
    const title = `Lista de ${trimmedName} - BARS`;

    // 1. Native share sheet (mobile) — close dialog first so the share
    //    sheet appears on a clean screen, matching the film modal share UX.
    if (navigator.share) {
      handleClose();
      try {
        await navigator.share({ url, title });
      } catch {
        // User cancelled or share failed — dialog already closed, nothing else to do
      }
      return;
    }

    // 2. Clipboard (Clipboard API → legacy execCommand fallback)
    const copied = await copyToClipboard(url);

    if (copied) {
      setShared(true);
      setTimeout(() => handleClose(), 1500);
    }
  }, [canShare, trimmedName, watchlist, handleClose]);

  return (
    <Modal
      isOpen={isOpen}
      onClose={handleClose}
      containerClassName="relative w-[calc(100%-2rem)] max-w-sm mx-auto bg-bars-bg-card rounded-bars-lg overflow-hidden"
      ariaLabelledBy="share-list-title"
    >
      {/* Header */}
      <div className="flex items-center justify-between px-5 pt-5 pb-2">
        <h2 id="share-list-title" className="flex items-center gap-2 font-display text-lg tracking-[0.5px] text-white">
          <ShareIcon size={18} />
          Compartir mi lista
        </h2>
        <button
          type="button"
          onClick={handleClose}
          className="flex h-8 w-8 items-center justify-center rounded-full border border-bars-border-light text-bars-text-muted transition-colors hover:text-white hover:border-white/40 cursor-pointer"
        >
          <XIcon size={16} />
        </button>
      </div>

      {/* Body */}
      <div className="px-5 py-4 space-y-4">
        <p className="text-sm text-bars-text-subtle">
          Ponele un nombre a tu lista para que quien la reciba sepa de quién es.
        </p>
        <input
          type="text"
          value={name}
          onChange={(e) => setName(e.target.value)}
          maxLength={MAX_LIST_NAME}
          placeholder="Ej: Lista de María"
          className="w-full rounded-bars-md bg-bars-bg-dark border border-bars-border-light px-4 py-3 text-sm text-white placeholder:text-bars-text-muted/50 focus:outline-none focus:border-bars-primary transition-colors"
          autoFocus
          onKeyDown={(e) => {
            if (e.key === 'Enter' && canShare) handleShare();
          }}
        />
      </div>

      {/* Footer */}
      <div className="flex items-center gap-3 px-5 pb-5">
        <button
          type="button"
          onClick={handleClose}
          className="flex-1 rounded-bars-md px-4 py-3 text-sm font-medium transition-colors cursor-pointer border border-bars-border-light text-bars-text-muted hover:text-white hover:border-white/40"
        >
          Cancelar
        </button>
        <button
          type="button"
          onClick={handleShare}
          disabled={!canShare}
          className="flex-1 rounded-bars-md px-4 py-3 text-sm font-semibold transition-colors cursor-pointer bg-bars-primary text-white hover:bg-bars-primary/90 disabled:opacity-40 disabled:cursor-not-allowed"
        >
          {shared ? 'Enlace copiado!' : 'Compartir'}
        </button>
      </div>
    </Modal>
  );
}
