import { useMemo } from 'react';

import { useData } from '../data/DataProvider';
import FilmCard from './FilmCard';
import { getSectionLabel, getVenueDisplay } from './utils';

export default function WatchlistStreamingSection({
  filterByWatchlist = true,
}: { filterByWatchlist?: boolean }) {
  const {
    alwaysAvailableScreenings,
    isAddedToWatchlist,
    toggleWatchlist,
    currentEdition,
    sections,
    openFilmModal,
  } = useData();

  const screenings = useMemo(
    () =>
      filterByWatchlist
        ? alwaysAvailableScreenings.filter((s) => isAddedToWatchlist(s))
        : alwaysAvailableScreenings,
    [filterByWatchlist, alwaysAvailableScreenings, isAddedToWatchlist],
  );

  if (screenings.length === 0) return null;

  return (
    <div>
      <h3 className="font-heading text-[24px] text-bars-text-primary mb-1">
        Streaming — Disponible Todo el Festival
      </h3>
      <p className="text-sm text-bars-text-subtle mb-5">
        Podés verlas en cualquier momento durante el festival.
      </p>
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-5">
        {screenings.map((screening) => (
          <FilmCard
            key={screening.raw}
            screening={screening}
            sectionLabel={getSectionLabel(screening, sections)}
            venueDisplay={getVenueDisplay(screening, currentEdition)}
            bookmarked={isAddedToWatchlist(screening)}
            onToggleWatchlist={() => toggleWatchlist(screening)}
            onOpenModal={() => openFilmModal(screening.movie)}
          />
        ))}
      </div>
    </div>
  );
}
