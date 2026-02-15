import Editions, { SingleEdition } from '@shared/ts/selection/Editions';
import { isTodayInBuenosAires, isTodayInBuenosAiresBetween } from '@shared/ts/selection/helpers';
import {
  isRegularStreamingScreening,
  isScreeningAlwaysAvailable,
  isTraditionalScreening,
  Screening,
} from '@shared/ts/selection/types';

import { MapPinIcon, MonitorPlayIcon } from '../icons';
import { getSpanishDayAbbr } from './helpers';

export default function ScreeningCard({
  screening,
  streamingLink,
  currentEdition,
  compact,
}: {
  screening: Screening;
  /** Per-movie streaming URL from `_movie_streamingLink` post meta. */
  streamingLink?: string;
  currentEdition: SingleEdition;
  compact?: boolean;
}) {
  const date = screening.isoDate ? new Date(screening.isoDate) : null;
  const isStreaming = screening.streaming;
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

  let buttonLabel = 'Tickets';
  let buttonLink: string | undefined = venueLink;
  let buttonEnabled = true;

  if (isScreeningAlwaysAvailable(screening)) {
    buttonLabel = 'Ver';
    buttonLink = streamingLink;
    const from = Editions.from(currentEdition);
    const to = Editions.to(currentEdition);
    if (from && to) {
      buttonEnabled = isTodayInBuenosAiresBetween(from, to);
    }
  } else if (isRegularStreamingScreening(screening)) {
    buttonLabel = 'Ver';
    buttonLink = streamingLink;
    buttonEnabled = isTodayInBuenosAires(new Date(screening.isoDate));
  }

  const dateBoxSize = compact ? 'h-11 w-10' : 'h-[52px] w-12';
  const dateBoxPadding = compact ? 'py-1.5 px-2.5' : 'py-2 px-3';
  const dayFontSize = compact ? 'text-[9px]' : 'text-[10px]';
  const numFontSize = compact ? 'text-xl' : 'text-2xl';
  const timeFontSize = compact ? 'text-lg' : 'text-lg';
  const venueFontSize = compact ? 'text-[8px]' : 'text-xs';
  const btnPadding = compact ? 'py-2 px-3.5' : 'py-2 px-4';
  const btnFontSize = compact ? 'text-[11px]' : 'text-xs';
  const iconSize = compact ? 18 : 22;

  return (
    <div className="flex items-center justify-between rounded-bars-md bg-bars-bg-card p-3 gap-3">
      <div className="flex items-center gap-3">
        {/* Date box or Online box */}
        {date ? (
          <div
            className={`flex flex-col items-center ${dateBoxSize} ${dateBoxPadding} rounded-[6px] bg-[rgba(139,0,0,0.2)]`}
          >
            <span className={`${dayFontSize} font-semibold tracking-[1px] text-white/60`}>
              {getSpanishDayAbbr(date)}
            </span>
            <span className={`font-display ${numFontSize} leading-none text-white`}>
              {date.getDate()}
            </span>
          </div>
        ) : isStreaming ? (
          <div
            className={`flex flex-col items-center justify-center ${dateBoxSize} ${dateBoxPadding} rounded-[6px] bg-[rgba(139,0,0,0.2)]`}
          >
            <MonitorPlayIcon size={iconSize} className="text-white/80" />
            <span className={`text-[7px] font-semibold tracking-[1px] text-white/60 mt-0.5`}>
              ONLINE
            </span>
          </div>
        ) : null}
        {/* Time + venue */}
        <div className="flex flex-col gap-0.5">
          {(time || isStreaming) && (
            <span className={`font-display ${timeFontSize} text-white`}>
              {time ?? 'A toda hora'}
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
      {/* Action button */}
      {buttonLink &&
        (buttonEnabled ? (
          <a
            href={buttonLink}
            target="_blank"
            rel="noopener noreferrer"
            onClick={(e) => e.stopPropagation()}
            className={`rounded-[6px] bg-bars-primary ${btnPadding} ${btnFontSize} font-semibold text-white hover:brightness-110 transition-all`}
          >
            {buttonLabel}
          </a>
        ) : (
          <span
            className={`rounded-[6px] bg-bars-primary opacity-40 cursor-not-allowed ${btnPadding} ${btnFontSize} font-semibold text-white`}
          >
            {buttonLabel}
          </span>
        ))}
    </div>
  );
}
