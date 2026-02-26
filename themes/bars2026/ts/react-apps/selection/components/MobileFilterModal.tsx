import { Modal } from '../../../components/modal/Modal';
import { useData } from '../data/DataProvider';
import { BookmarkIcon, FilterIcon, XIcon } from './icons';
import { getColorForList } from './sharedListColors';

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
    sectionMovieCounts,
    sharedLists,
    activeSharedListIds,
    toggleSharedList,
    requestDeleteSharedList,
    sharedListMovieCountsForTab,
    listSubTab,
    watchlistListFilters,
    toggleWatchlistListFilter,
    watchlistOverlapCounts,
  } = useData();

  const isWatchlistTab = activeTab.type === 'watchlist';
  const filterCount =
    activeCategories.length +
    (watchlistOnly && !isWatchlistTab ? 1 : 0) +
    (isWatchlistTab ? 0 : activeSharedListIds.length) +
    (isWatchlistTab ? watchlistListFilters.length : 0);
  const hasActiveFilters = filterCount > 0;

  // Other lists for overlap pills (watchlist tab only)
  const otherSharedLists = sharedLists.filter(l => l.id !== listSubTab);
  const hasOtherLists = isWatchlistTab && (
    (listSubTab !== 'personal' ? 1 : 0) + otherSharedLists.length > 0
  );

  const categories = Object.entries(availableSections)
    .sort(([, a], [, b]) => a.localeCompare(b, 'es'));

  const clearAll = () => {
    setActiveCategories([]);
    if (!isWatchlistTab) setWatchlistOnly(false);
    // Deactivate all shared list filters (only when not on watchlist tab — sub-tabs replace them)
    if (!isWatchlistTab) {
      for (const list of sharedLists) {
        if (activeSharedListIds.includes(list.id)) toggleSharedList(list.id);
      }
    }
    // Clear list overlap filters (watchlist tab only)
    if (isWatchlistTab) {
      for (const id of watchlistListFilters) {
        toggleWatchlistListFilter(id);
      }
    }
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
        {/* List pills: watchlist + shared lists (hidden on watchlist tab) */}
        {!isWatchlistTab && (
          <div>
            <h3 className="text-xs font-semibold uppercase tracking-wider text-bars-text-muted mb-3">
              Filtrar por lista
            </h3>
            <div className="flex flex-wrap gap-2.5">
              <button
                type="button"
                onClick={() => setWatchlistOnly(!watchlistOnly)}
                className={`inline-flex items-center gap-2 rounded-bars-pill px-5 py-3 text-sm font-medium transition-colors cursor-pointer
                  ${watchlistOnly
                    ? 'border text-white'
                    : 'border border-bars-border-light text-bars-text-muted hover:text-white hover:border-white/40'
                  }
                `}
                style={watchlistOnly ? { borderColor: '#8b0000', backgroundColor: '#8b000020' } : undefined}
              >
                <BookmarkIcon size={14} filled className="text-bars-primary" />
                Mi lista
                <span className="rounded-full bg-white/20 py-0.5 px-2 text-xs leading-none font-semibold text-white tabular-nums">
                  {watchlistCountForTab}
                </span>
              </button>

              {sharedLists.map((list, index) => {
                const color = getColorForList(index);
                const isActive = activeSharedListIds.includes(list.id);
                return (
                  <button
                    key={list.id}
                    type="button"
                    onClick={() => toggleSharedList(list.id)}
                    className={`inline-flex items-center gap-2 rounded-bars-pill pl-4 pr-4 py-3 text-sm font-medium transition-colors cursor-pointer
                      ${isActive
                        ? 'border text-white'
                        : 'border border-bars-border-light text-bars-text-muted hover:text-white hover:border-white/40'
                      }
                    `}
                    style={isActive ? { borderColor: color, backgroundColor: `${color}20` } : undefined}
                  >
                    <span
                      className="inline-block w-3 h-3 rounded-full flex-shrink-0"
                      style={{ backgroundColor: color }}
                    />
                    {list.name}
                    <span className="rounded-full bg-white/20 py-0.5 px-2 text-xs leading-none font-semibold text-white tabular-nums">
                      {sharedListMovieCountsForTab.get(list.id) ?? 0}
                    </span>
                  </button>
                );
              })}
            </div>
          </div>
        )}

        {/* List overlap pills (watchlist tab only) */}
        {hasOtherLists && (
          <div>
            <h3 className="text-xs font-semibold uppercase tracking-wider text-bars-text-muted mb-3">
              Filtrar por lista
            </h3>
            <div className="flex flex-wrap gap-2.5">
              {/* "Mi lista" pill (when viewing a shared sub-tab) */}
              {listSubTab !== 'personal' && (
                <button
                  type="button"
                  onClick={() => toggleWatchlistListFilter('personal')}
                  className={`inline-flex items-center gap-2 rounded-bars-pill px-5 py-3 text-sm font-medium transition-colors cursor-pointer
                    ${watchlistListFilters.includes('personal')
                      ? 'border text-white'
                      : 'border border-bars-border-light text-bars-text-muted hover:text-white hover:border-white/40'
                    }
                  `}
                  style={watchlistListFilters.includes('personal') ? { borderColor: '#8b0000', backgroundColor: '#8b000020' } : undefined}
                >
                  <BookmarkIcon size={14} filled className="text-bars-primary" />
                  Mi lista
                  <span className="rounded-full bg-white/20 py-0.5 px-2 text-xs leading-none font-semibold text-white tabular-nums">
                    {watchlistOverlapCounts.get('personal') ?? 0}
                  </span>
                </button>
              )}
              {/* Shared list overlap pills (skip active sub-tab) */}
              {otherSharedLists.map((list) => {
                const globalIndex = sharedLists.indexOf(list);
                const color = getColorForList(globalIndex);
                const isActive = watchlistListFilters.includes(list.id);
                return (
                  <button
                    key={list.id}
                    type="button"
                    onClick={() => toggleWatchlistListFilter(list.id)}
                    className={`inline-flex items-center gap-2 rounded-bars-pill px-5 py-3 text-sm font-medium transition-colors cursor-pointer
                      ${isActive
                        ? 'border text-white'
                        : 'border border-bars-border-light text-bars-text-muted hover:text-white hover:border-white/40'
                      }
                    `}
                    style={isActive ? { borderColor: color, backgroundColor: `${color}20` } : undefined}
                  >
                    <span
                      className="inline-block w-3 h-3 rounded-full flex-shrink-0"
                      style={{ backgroundColor: color }}
                    />
                    {list.name}
                    <span className="rounded-full bg-white/20 py-0.5 px-2 text-xs leading-none font-semibold text-white tabular-nums">
                      {watchlistOverlapCounts.get(list.id) ?? 0}
                    </span>
                  </button>
                );
              })}
            </div>
          </div>
        )}

        {/* Sections group */}
        <div>
          <h3 className="text-xs font-semibold uppercase tracking-wider text-bars-text-muted mb-3">
            Filtrar por categoría
          </h3>
          <div className="flex flex-wrap gap-2.5">
            {/* "Todos" pill */}
            <button
              type="button"
              onClick={() => setActiveCategories([])}
              className={`inline-flex items-center gap-2 rounded-bars-pill px-5 py-3 text-sm font-medium transition-colors cursor-pointer
                ${activeCategories.length === 0
                  ? 'bg-bars-primary border border-bars-primary text-white'
                  : 'border border-bars-border-light text-bars-text-muted hover:text-white hover:border-white/40'
                }
              `}
            >
              Todos
              <span className="rounded-full bg-white/20 py-0.5 px-2 text-xs leading-none font-semibold text-white tabular-nums">
                {Array.from(sectionMovieCounts.values()).reduce((a, b) => a + b, 0)}
              </span>
            </button>

            {/* Category pills */}
            {categories.map(([id, label]) => (
              <button
                key={id}
                type="button"
                onClick={() => toggleCategory(id)}
                className={`inline-flex items-center gap-2 rounded-bars-pill px-5 py-3 text-sm font-medium transition-colors cursor-pointer
                  ${activeCategories.includes(id)
                    ? 'bg-bars-primary border border-bars-primary text-white'
                    : 'border border-bars-border-light text-bars-text-muted hover:text-white hover:border-white/40'
                  }
                `}
              >
                {label}
                <span className="rounded-full bg-white/20 py-0.5 px-2 text-xs leading-none font-semibold text-white tabular-nums">
                  {sectionMovieCounts.get(id) ?? 0}
                </span>
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
