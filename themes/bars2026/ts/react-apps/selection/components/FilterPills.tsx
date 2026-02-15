import { useData } from '../data/DataProvider';
import { BookmarkIcon } from './icons';

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
  } = useData();

  const isWatchlistTab = activeTab.type === 'watchlist';

  const categories = Object.entries(availableSections)
    .sort(([, a], [, b]) => a.localeCompare(b, 'es'));

  return (
    <div className="flex flex-wrap items-center gap-2">
      <span className="text-xs text-bars-text-muted font-medium mr-1">
        Filtrar:
      </span>

      {/* Watchlist toggle pill (hidden when Mi Lista tab is active) */}
      {!isWatchlistTab && (
        <>
          <button
            type="button"
            onClick={() => setWatchlistOnly(!watchlistOnly)}
            className={`inline-flex items-center gap-1.5 rounded-bars-pill px-4 py-2 text-xs font-medium transition-colors cursor-pointer
              ${watchlistOnly
                ? 'bg-bars-primary border border-bars-primary text-white'
                : 'border border-bars-border-light text-bars-text-muted hover:text-white hover:border-white/40'
              }
            `}
          >
            <BookmarkIcon size={12} filled={watchlistOnly} />
            Solo en mi lista
            <span className="rounded-full bg-white/20 py-0.5 px-2 text-[11px] leading-none font-semibold text-white tabular-nums">
              {watchlistCountForTab}
            </span>
          </button>

          {/* Divider */}
          <div className="w-px h-5 bg-bars-divider mx-1" />
        </>
      )}

      {/* "Todos" pill */}
      <button
        type="button"
        onClick={() => setActiveCategories([])}
        className={`rounded-bars-pill px-4 py-2 text-xs font-medium transition-colors cursor-pointer
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
          className={`rounded-bars-pill px-4 py-2 text-xs font-medium transition-colors cursor-pointer
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
  );
}
