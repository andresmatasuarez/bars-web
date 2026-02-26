import { useData } from '../data/DataProvider';
import { ChevronDownIcon, FilterIcon } from './icons';

interface MobileFilterButtonProps {
  onOpen: () => void;
}

export default function MobileFilterButton({ onOpen }: MobileFilterButtonProps) {
  const { activeCategories, watchlistOnly, activeTab, activeSharedListIds, watchlistListFilters } =
    useData();

  const isWatchlistTab = activeTab.type === 'watchlist';
  const filterCount =
    activeCategories.length +
    (watchlistOnly && !isWatchlistTab ? 1 : 0) +
    (isWatchlistTab ? 0 : activeSharedListIds.length) +
    (isWatchlistTab ? watchlistListFilters.length : 0);
  const hasActiveFilters = filterCount > 0;

  return (
    <button
      type="button"
      onClick={onOpen}
      className="flex w-full items-center gap-2 rounded-bars-md bg-bars-bg-card px-4 py-2 text-xs font-medium text-bars-text-muted transition-colors cursor-pointer hover:text-white"
    >
      <FilterIcon size={14} />
      <span className="flex-1 text-left">
        {hasActiveFilters
          ? `${filterCount} filtro${filterCount > 1 ? 's' : ''} activo${filterCount > 1 ? 's' : ''}`
          : 'Filtrar por sección'}
      </span>
      {hasActiveFilters && (
        <span className="flex h-4 min-w-4 items-center justify-center rounded-full bg-bars-primary px-1 text-[10px] font-semibold text-white tabular-nums">
          {filterCount}
        </span>
      )}
      <ChevronDownIcon size={14} />
    </button>
  );
}
