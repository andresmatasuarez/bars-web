import { useData } from '../data/DataProvider';
import { BookmarkIcon } from './icons';
import { getColorForList } from './sharedListColors';

export default function FilterPills() {
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
    sharedListMovieCountsForTab,
    listSubTab,
    watchlistListFilters,
    toggleWatchlistListFilter,
    watchlistOverlapCounts,
  } = useData();

  const isWatchlistTab = activeTab.type === 'watchlist';

  // Other lists = lists not currently being viewed in the sub-tab (for overlap pills)
  const otherSharedLists = sharedLists.filter(l => l.id !== listSubTab);
  const hasOtherLists = isWatchlistTab && (
    (listSubTab !== 'personal' ? 1 : 0) + otherSharedLists.length > 0
  );

  const categories = Object.entries(availableSections)
    .sort(([, a], [, b]) => a.localeCompare(b, 'es'));

  const showListRow = isWatchlistTab ? hasOtherLists : true;

  return (
    <div className="grid items-start gap-x-3 gap-y-2" style={{ gridTemplateColumns: 'auto 1fr' }}>
      {/* Row 1 — List pills */}
      {showListRow && (
        <>
          <span className="text-xs text-bars-text-muted font-medium text-right whitespace-nowrap py-2">
            Filtrar por lista:
          </span>
          <div className="flex flex-wrap items-center gap-2">
            {/* List overlap pills (watchlist tab) */}
            {isWatchlistTab && (
              <>
                {/* "Mi lista" pill (when viewing a shared sub-tab) */}
                {listSubTab !== 'personal' && (
                  <button
                    type="button"
                    onClick={() => toggleWatchlistListFilter('personal')}
                    className={`inline-flex items-center gap-1.5 rounded-bars-pill px-4 py-2 text-xs font-medium transition-colors cursor-pointer
                      ${watchlistListFilters.includes('personal')
                        ? 'border text-white'
                        : 'border border-bars-border-light text-bars-text-muted hover:text-white hover:border-white/40'
                      }
                    `}
                    style={watchlistListFilters.includes('personal') ? { borderColor: '#8b0000', backgroundColor: '#8b000020' } : undefined}
                  >
                    <BookmarkIcon size={12} filled className="text-bars-primary" />
                    Mi lista
                    <span className="rounded-full bg-white/20 py-0.5 px-2 text-[11px] leading-none font-semibold text-white tabular-nums">
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
                      className={`inline-flex items-center gap-1.5 rounded-bars-pill px-4 py-2 text-xs font-medium transition-colors cursor-pointer
                        ${isActive
                          ? 'border text-white'
                          : 'border border-bars-border-light text-bars-text-muted hover:text-white hover:border-white/40'
                        }
                      `}
                      style={isActive ? { borderColor: color, backgroundColor: `${color}20` } : undefined}
                    >
                      <span
                        className="inline-block w-2.5 h-2.5 rounded-full flex-shrink-0"
                        style={{ backgroundColor: color }}
                      />
                      {list.name}
                      <span className="rounded-full bg-white/20 py-0.5 px-2 text-[11px] leading-none font-semibold text-white tabular-nums">
                        {watchlistOverlapCounts.get(list.id) ?? 0}
                      </span>
                    </button>
                  );
                })}
              </>
            )}

            {/* List pills: watchlist + shared lists (non-watchlist tabs) */}
            {!isWatchlistTab && (
              <>
                <button
                  type="button"
                  onClick={() => setWatchlistOnly(!watchlistOnly)}
                  className={`inline-flex items-center gap-1.5 rounded-bars-pill px-4 py-2 text-xs font-medium transition-colors cursor-pointer
                    ${watchlistOnly
                      ? 'border text-white'
                      : 'border border-bars-border-light text-bars-text-muted hover:text-white hover:border-white/40'
                    }
                  `}
                  style={watchlistOnly ? { borderColor: '#8b0000', backgroundColor: '#8b000020' } : undefined}
                >
                  <BookmarkIcon size={12} filled className="text-bars-primary" />
                  Mi lista
                  <span className="rounded-full bg-white/20 py-0.5 px-2 text-[11px] leading-none font-semibold text-white tabular-nums">
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
                      className={`inline-flex items-center gap-1.5 rounded-bars-pill pl-3.5 pr-3.5 py-2 text-xs font-medium transition-colors cursor-pointer
                        ${isActive
                          ? 'border text-white'
                          : 'border border-bars-border-light text-bars-text-muted hover:text-white hover:border-white/40'
                        }
                      `}
                      style={isActive ? { borderColor: color, backgroundColor: `${color}20` } : undefined}
                    >
                      <span
                        className="inline-block w-2.5 h-2.5 rounded-full flex-shrink-0"
                        style={{ backgroundColor: color }}
                      />
                      {list.name}
                      <span className="rounded-full bg-white/20 py-0.5 px-2 text-[11px] leading-none font-semibold text-white tabular-nums">
                        {sharedListMovieCountsForTab.get(list.id) ?? 0}
                      </span>
                    </button>
                  );
                })}
              </>
            )}
          </div>
        </>
      )}

      {/* Row 2 — Category pills */}
      <span className="text-xs text-bars-text-muted font-medium text-right whitespace-nowrap py-2">
        Filtrar por categoría:
      </span>
      <div className="flex flex-wrap items-center gap-2">
        {/* "Todos" pill */}
        <button
          type="button"
          onClick={() => setActiveCategories([])}
          className={`inline-flex items-center gap-1.5 rounded-bars-pill px-4 py-2 text-xs font-medium transition-colors cursor-pointer
            ${activeCategories.length === 0
              ? 'bg-bars-primary border border-bars-primary text-white'
              : 'border border-bars-border-light text-bars-text-muted hover:text-white hover:border-white/40'
            }
          `}
        >
          Todos
          <span className="rounded-full bg-white/20 py-0.5 px-2 text-[11px] leading-none font-semibold text-white tabular-nums">
            {Array.from(sectionMovieCounts.values()).reduce((a, b) => a + b, 0)}
          </span>
        </button>

        {/* Category pills */}
        {categories.map(([id, label]) => (
          <button
            key={id}
            type="button"
            onClick={() => toggleCategory(id)}
            className={`inline-flex items-center gap-1.5 rounded-bars-pill px-4 py-2 text-xs font-medium transition-colors cursor-pointer
              ${activeCategories.includes(id)
                ? 'bg-bars-primary border border-bars-primary text-white'
                : 'border border-bars-border-light text-bars-text-muted hover:text-white hover:border-white/40'
              }
            `}
          >
            {label}
            <span className="rounded-full bg-white/20 py-0.5 px-2 text-[11px] leading-none font-semibold text-white tabular-nums">
              {sectionMovieCounts.get(id) ?? 0}
            </span>
          </button>
        ))}
      </div>
    </div>
  );
}
