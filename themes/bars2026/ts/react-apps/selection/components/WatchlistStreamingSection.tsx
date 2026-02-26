import { useMemo, useState } from 'react';

import { useData } from '../data/DataProvider';
import FilmCard from './FilmCard';
import { ChevronDownIcon } from './icons';
import { getColorForList } from './sharedListColors';
import { getSectionLabel, getVenueDisplay } from './utils';

export default function WatchlistStreamingSection({
  filterByWatchlist = true,
}: {
  filterByWatchlist?: boolean;
}) {
  const {
    alwaysAvailableScreenings,
    isAddedToWatchlist,
    isInActiveSubTabList,
    toggleWatchlist,
    currentEdition,
    sections,
    openFilmModal,
    sharedLists,
    getSharedListIdsForScreening,
  } = useData();

  const [collapsed, setCollapsed] = useState(false);

  const screenings = useMemo(
    () =>
      filterByWatchlist
        ? alwaysAvailableScreenings.filter((s) => isInActiveSubTabList(s))
        : alwaysAvailableScreenings,
    [filterByWatchlist, alwaysAvailableScreenings, isInActiveSubTabList],
  );

  const sharedListColorMap = useMemo(() => {
    const map = new Map<string, { name: string; color: string }>();
    sharedLists.forEach((list, index) => {
      map.set(list.id, { name: list.name, color: getColorForList(index) });
    });
    return map;
  }, [sharedLists]);

  if (screenings.length === 0) return null;

  return (
    <div>
      <div
        className="relative flex items-center gap-3 cursor-pointer"
        onClick={() => setCollapsed((c) => !c)}
      >
        <ChevronDownIcon
          size={16}
          className={`lg:absolute lg:-left-7 lg:top-1/2 lg:-translate-y-1/2 text-white/50 transition-transform duration-200 shrink-0${collapsed ? ' -rotate-90' : ''}`}
        />
        <span className="bg-bars-primary/25 text-white text-xs font-semibold tracking-wide uppercase px-3 py-1 rounded-full">
          streaming
        </span>
        <div className="flex-1 h-px bg-white/[0.08]" />
      </div>
      {!collapsed && (
        <>
          <p className="text-sm text-bars-link-accent/70 italic mt-2 mb-5 border-l-2 border-bars-link-accent/25 pl-3">
            Podés ver las siguientes películas por streaming cualquier día, a cualquier hora,
            durante el transcurso del festival.
          </p>
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-5">
            {screenings.map((screening) => {
              const listIds = getSharedListIdsForScreening(screening);
              const colors =
                listIds.length > 0
                  ? listIds.map((id) => sharedListColorMap.get(id)!).filter(Boolean)
                  : undefined;
              return (
                <FilmCard
                  key={`${screening.movie.id}-${screening.raw}`}
                  screening={screening}
                  sectionLabel={getSectionLabel(screening, sections)}
                  venueDisplay={getVenueDisplay(screening, currentEdition)}
                  bookmarked={isAddedToWatchlist(screening)}
                  onToggleWatchlist={() => toggleWatchlist(screening)}
                  onOpenModal={() => openFilmModal(screening.movie)}
                  sharedListColors={colors}
                />
              );
            })}
          </div>
        </>
      )}
    </div>
  );
}
