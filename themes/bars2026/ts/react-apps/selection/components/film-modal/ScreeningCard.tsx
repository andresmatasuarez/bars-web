import Editions, { SingleEdition } from '@shared/ts/selection/Editions';
import { isTraditionalScreening, Screening } from '@shared/ts/selection/types';

import { MapPinIcon } from '../icons';
import { getSpanishDayAbbr } from './helpers';

export default function ScreeningCard({
  screening,
  currentEdition,
  compact,
}: {
  screening: Screening;
  currentEdition: SingleEdition;
  compact?: boolean;
}) {
  const date = screening.isoDate ? new Date(screening.isoDate) : null;
  let venueName = '';
  let venueLink: string | undefined;

  try {
    const venues = Editions.venues(currentEdition);
    const venue = venues[screening.venue];
    if (venue) {
      venueName = venue.name;
      venueLink = venue.link;
    }
  } catch {
    venueName = screening.venue;
  }

  let roomName = '';
  if (isTraditionalScreening(screening) && screening.room) {
    roomName = screening.room;
  }

  const venueDisplay = roomName ? `${venueName} - ${roomName}` : venueName;
  const time = isTraditionalScreening(screening) ? screening.time : null;

  const dateBoxSize = compact ? 'h-11 w-10' : 'h-[52px] w-12';
  const dateBoxPadding = compact ? 'py-1.5 px-2.5' : 'py-2 px-3';
  const dayFontSize = compact ? 'text-[9px]' : 'text-[10px]';
  const numFontSize = compact ? 'text-xl' : 'text-2xl';
  const timeFontSize = compact ? 'text-lg' : 'text-lg';
  const venueFontSize = compact ? 'text-[11px]' : 'text-xs';
  const btnPadding = compact ? 'py-2 px-3.5' : 'py-2 px-4';
  const btnFontSize = compact ? 'text-[11px]' : 'text-xs';

  return (
    <div className="flex items-center justify-between rounded-bars-md bg-bars-bg-card p-3 gap-3">
      <div className="flex items-center gap-3">
        {/* Date box */}
        {date && (
          <div
            className={`flex flex-col items-center ${dateBoxSize} ${dateBoxPadding} rounded-[6px] bg-[rgba(139,0,0,0.2)]`}
          >
            <span
              className={`${dayFontSize} font-semibold tracking-[1px] text-white/60`}
            >
              {getSpanishDayAbbr(date)}
            </span>
            <span className={`font-display ${numFontSize} leading-none text-white`}>
              {date.getDate()}
            </span>
          </div>
        )}
        {/* Time + venue */}
        <div className="flex flex-col gap-0.5">
          {time && (
            <span className={`font-display ${timeFontSize} text-white`}>
              {time}
            </span>
          )}
          {venueDisplay && (
            <span className={`flex items-center gap-1 ${venueFontSize} text-white/40`}>
              <MapPinIcon size={12} className="flex-shrink-0" />
              {venueDisplay}
            </span>
          )}
        </div>
      </div>
      {/* Tickets button */}
      {venueLink && (
        <a
          href={venueLink}
          target="_blank"
          rel="noopener noreferrer"
          onClick={(e) => e.stopPropagation()}
          className={`rounded-[6px] bg-bars-primary ${btnPadding} ${btnFontSize} font-semibold text-white hover:brightness-110 transition-all`}
        >
          Tickets
        </a>
      )}
    </div>
  );
}
