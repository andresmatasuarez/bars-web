import { WatchlistEntry } from '@shared/ts/useWatchlist';

import { Modal } from '../../../components/modal/Modal';
import { SharedList } from '../data/useSharedLists';
import { XIcon } from './icons';
import { getColorForList } from './sharedListColors';

interface ReplaceListDialogProps {
  isOpen: boolean;
  onClose: () => void;
  pendingList: { name: string; entries: WatchlistEntry[] } | null;
  existingLists: SharedList[];
  emptyListIds: Set<string>;
  onReplace: (removeId: string) => void;
}

export default function ReplaceListDialog({
  isOpen,
  onClose,
  pendingList,
  existingLists,
  emptyListIds,
  onReplace,
}: ReplaceListDialogProps) {
  if (!pendingList) return null;

  return (
    <Modal
      isOpen={isOpen}
      onClose={onClose}
      containerClassName="relative w-[calc(100%-2rem)] max-w-sm mx-auto bg-bars-bg-card rounded-bars-lg overflow-hidden"
      ariaLabelledBy="replace-list-title"
    >
      {/* Header */}
      <div className="flex items-center justify-between px-5 pt-5 pb-2">
        <h2 id="replace-list-title" className="font-display text-lg tracking-[0.5px] text-white">
          Ya tenés 3 listas guardadas
        </h2>
        <button
          type="button"
          onClick={onClose}
          className="flex h-8 w-8 items-center justify-center rounded-full border border-bars-border-light text-bars-text-muted transition-colors hover:text-white hover:border-white/40 cursor-pointer"
        >
          <XIcon size={16} />
        </button>
      </div>

      {/* Body */}
      <div className="px-5 py-4 space-y-3">
        <p className="text-sm text-bars-text-subtle">
          Elegí una para reemplazar con &quot;{pendingList.name}&quot;:
        </p>

        <div className="space-y-2">
          {existingLists.map((list, index) => {
            const color = getColorForList(index);
            const isEmpty = emptyListIds.has(list.id);
            return (
              <div
                key={list.id}
                className="flex items-center justify-between gap-3 rounded-bars-md bg-bars-bg-dark px-4 py-3"
              >
                <span className="flex items-center gap-2 text-sm text-white min-w-0">
                  <span
                    className="inline-block w-2.5 h-2.5 rounded-full flex-shrink-0"
                    style={{ backgroundColor: color }}
                  />
                  <span className="min-w-0">
                    <span className="truncate block">{list.name}</span>
                    {isEmpty && (
                      <span className="block text-xs text-bars-text-muted/60 truncate">
                        sin películas en esta edición
                      </span>
                    )}
                  </span>
                </span>
                <button
                  type="button"
                  onClick={() => onReplace(list.id)}
                  className="flex-shrink-0 rounded-bars-pill px-3 py-1.5 text-xs font-medium bg-bars-primary/15 border border-bars-primary/30 text-bars-primary hover:bg-bars-primary/25 transition-colors cursor-pointer"
                >
                  Reemplazar
                </button>
              </div>
            );
          })}
        </div>
      </div>

      {/* Footer */}
      <div className="px-5 pb-5">
        <button
          type="button"
          onClick={onClose}
          className="w-full rounded-bars-md px-4 py-3 text-sm font-medium transition-colors cursor-pointer border border-bars-border-light text-bars-text-muted hover:text-white hover:border-white/40"
        >
          Cancelar
        </button>
      </div>
    </Modal>
  );
}
