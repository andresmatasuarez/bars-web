import {
  RegularStreamingScreening,
  ScreeningWithMovie,
  TraditionalScreening,
} from '@shared/ts/selection/types';

import FilmCard from './FilmCard';
import { ClockIcon } from './icons';

type Props = {
  time: string;
  screenings: ScreeningWithMovie<TraditionalScreening | RegularStreamingScreening>[];
  hideDivider?: boolean;
};

export default function TimeSlot({ time, screenings, hideDivider }: Props) {
  return (
    <div>
      {/* Time header */}
      <div className="flex items-center gap-3 mb-4">
        <ClockIcon size={20} className="text-bars-primary flex-shrink-0" />
        <span className="font-display text-[24px] lg:text-[32px] leading-none text-bars-text-primary">
          {time}
        </span>
        {!hideDivider && <div className="flex-1 h-px bg-bars-divider" />}
      </div>

      {/* Film grid */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-5">
        {screenings.map((screening) => (
          <FilmCard key={screening.raw} screening={screening} />
        ))}
      </div>
    </div>
  );
}
