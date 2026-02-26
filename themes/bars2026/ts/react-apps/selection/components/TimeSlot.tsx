import {
  RegularStreamingScreening,
  ScreeningWithMovie,
  TraditionalScreening,
} from '@shared/ts/selection/types';
import { useMemo } from 'react';

import { useData } from '../data/DataProvider';
import FilmCard from './FilmCard';
import { ClockIcon, MonitorPlayIcon } from './icons';
import { getColorForList } from './sharedListColors';
import { getSectionLabel, getVenueDisplay } from './utils';

type Props = {
  time: string;
  screenings: ScreeningWithMovie<TraditionalScreening | RegularStreamingScreening>[];
  hideDivider?: boolean;
};

export default function TimeSlot({ time, screenings, hideDivider }: Props) {
  const {
    currentEdition,
    sections,
    isAddedToWatchlist,
    toggleWatchlist,
    openFilmModal,
    sharedLists,
    getSharedListIdsForScreening,
  } = useData();

  const sharedListColorMap = useMemo(() => {
    const map = new Map<string, { name: string; color: string }>();
    sharedLists.forEach((list, index) => {
      map.set(list.id, { name: list.name, color: getColorForList(index) });
    });
    return map;
  }, [sharedLists]);

  const isStreaming = time === 'Online';
  const Icon = isStreaming ? MonitorPlayIcon : ClockIcon;
  const label = isStreaming ? 'Streaming - Todo el día' : time;

  return (
    <div>
      {/* Time header */}
      <div className="flex items-center gap-3 mb-4">
        <Icon size={20} className="text-bars-primary flex-shrink-0" />
        <span className="font-display text-[24px] lg:text-[32px] leading-none text-bars-text-primary">
          {label}
        </span>
        {!hideDivider && <div className="flex-1 h-px bg-bars-divider" />}
      </div>

      {/* Film grid */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-5">
        {screenings.map((screening) => {
          const listIds = getSharedListIdsForScreening(screening);
          const colors = listIds.length > 0
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
    </div>
  );
}
