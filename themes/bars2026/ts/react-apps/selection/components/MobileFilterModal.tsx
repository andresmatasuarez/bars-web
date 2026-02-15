import { Modal } from '../../../components/modal/Modal';
import { useData } from '../data/DataProvider';
import { BookmarkIcon, FilterIcon, XIcon } from './icons';

interface MobileFilterModalProps {
  isOpen: boolean;
  onClose: () => void;
}

export default function MobileFilterModal({ isOpen, onClose }: MobileFilterModalProps) {
  const {
    availableSections,
    activeCategories,
    setActiveCategories,
    toggleCategory,
    activeTab,
    watchlistOnly,
    setWatchlistOnly,
    watchlistCountForTab,
  } = useData();

  const isWatchlistTab = activeTab.type === 'watchlist';
  const filterCount = activeCategories.length + (watchlistOnly && !isWatchlistTab ? 1 : 0);
  const hasActiveFilters = filterCount > 0;

  const categories = Object.entries(availableSections)
    .sort(([, a], [, b]) => a.localeCompare(b, 'es'));

  const clearAll = () => {
    setActiveCategories([]);
    if (!isWatchlistTab) setWatchlistOnly(false);
  };

  return (
    <Modal
      isOpen={isOpen}
      onClose={onClose}
      containerClassName="relative w-full h-full bg-bars-bg-dark flex flex-col"
      ariaLabelledBy="mobile-filter-title"
    >
      {/* Header */}
      <div className="flex items-center justify-between px-5 py-4 border-b border-bars-divider">
        <h2 id="mobile-filter-title" className="flex items-center gap-2.5 font-display text-xl tracking-[1px] text-white">
          <FilterIcon size={20} />
          Filtrar
        </h2>
        <button
          type="button"
          onClick={onClose}
          className="flex h-9 w-9 items-center justify-center rounded-full border border-bars-border-light text-bars-text-muted transition-colors hover:text-white hover:border-white/40 cursor-pointer"
        >
          <XIcon size={18} />
        </button>
      </div>

      {/* Scrollable body */}
      <div className="flex-1 overflow-y-auto px-5 py-5 space-y-6">
        {/* Watchlist group (hidden on watchlist tab) */}
        {!isWatchlistTab && (
          <div>
            <h3 className="text-xs font-semibold uppercase tracking-wider text-bars-text-muted mb-3">
              Mi lista
            </h3>
            <button
              type="button"
              onClick={() => setWatchlistOnly(!watchlistOnly)}
              className={`inline-flex items-center gap-2 rounded-bars-pill px-5 py-3 text-sm font-medium transition-colors cursor-pointer
                ${watchlistOnly
                  ? 'bg-bars-primary border border-bars-primary text-white'
                  : 'border border-bars-border-light text-bars-text-muted hover:text-white hover:border-white/40'
                }
              `}
            >
              <BookmarkIcon size={14} filled={watchlistOnly} />
              Solo en mi lista
              <span className="rounded-full bg-white/20 py-0.5 px-2 text-xs leading-none font-semibold text-white tabular-nums">
                {watchlistCountForTab}
              </span>
            </button>
          </div>
        )}

        {/* Sections group */}
        <div>
          <h3 className="text-xs font-semibold uppercase tracking-wider text-bars-text-muted mb-3">
            Secciones
          </h3>
          <div className="flex flex-wrap gap-2.5">
            {/* "Todos" pill */}
            <button
              type="button"
              onClick={() => setActiveCategories([])}
              className={`rounded-bars-pill px-5 py-3 text-sm font-medium transition-colors cursor-pointer
                ${activeCategories.length === 0
                  ? 'bg-bars-primary border border-bars-primary text-white'
                  : 'border border-bars-border-light text-bars-text-muted hover:text-white hover:border-white/40'
                }
              `}
            >
              Todos
            </button>

            {/* Category pills */}
            {categories.map(([id, label]) => (
              <button
                key={id}
                type="button"
                onClick={() => toggleCategory(id)}
                className={`rounded-bars-pill px-5 py-3 text-sm font-medium transition-colors cursor-pointer
                  ${activeCategories.includes(id)
                    ? 'bg-bars-primary border border-bars-primary text-white'
                    : 'border border-bars-border-light text-bars-text-muted hover:text-white hover:border-white/40'
                  }
                `}
              >
                {label}
              </button>
            ))}
          </div>
        </div>
      </div>

      {/* Footer */}
      <div className="flex items-center gap-3 px-5 py-4 border-t border-bars-divider">
        <button
          type="button"
          onClick={clearAll}
          disabled={!hasActiveFilters}
          className="flex-1 rounded-bars-md px-4 py-3 text-sm font-medium transition-colors cursor-pointer border border-bars-border-light text-bars-text-muted hover:text-white hover:border-white/40 disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:text-bars-text-muted disabled:hover:border-bars-border-light"
        >
          Limpiar filtros
        </button>
        <button
          type="button"
          onClick={onClose}
          className="flex-1 rounded-bars-md px-4 py-3 text-sm font-semibold transition-colors cursor-pointer bg-bars-primary text-white hover:bg-bars-primary/90"
        >
          Ver resultados
        </button>
      </div>
    </Modal>
  );
}
