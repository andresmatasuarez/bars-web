import { Screening, ScreeningWithMovie } from '@shared/ts/types';
import { memo, useRef } from 'react';

import BookmarkButton from './BookmarkButton';
import useLetterboxCrop from './film-modal/useLetterboxCrop';
import { MapPinIcon } from './icons';
import ShareButton from './ShareButton';

const Thumbnail = memo(function Thumbnail({ html }: { html: string }) {
  const ref = useRef<HTMLDivElement>(null);
  useLetterboxCrop(ref);

  return (
    <div
      ref={ref}
      className="w-full h-full [&_img]:w-full [&_img]:h-full [&_img]:object-cover"
      dangerouslySetInnerHTML={{ __html: html }}
    />
  );
});

type SharedListColor = { name: string; color: string };

type Props = {
  screening: ScreeningWithMovie<Screening>;
  sectionLabel: string;
  venueDisplay: string;
  bookmarked: boolean;
  onToggleWatchlist: () => void;
  onOpenModal: () => void;
  sharedListColors?: SharedListColor[];
};

function arePropsEqual(prev: Props, next: Props): boolean {
  if (
    prev.screening.raw !== next.screening.raw ||
    prev.sectionLabel !== next.sectionLabel ||
    prev.venueDisplay !== next.venueDisplay ||
    prev.bookmarked !== next.bookmarked
  ) {
    return false;
  }
  const prevColors = prev.sharedListColors;
  const nextColors = next.sharedListColors;
  if (!prevColors && !nextColors) return true;
  if (!prevColors || !nextColors) return false;
  if (prevColors.length !== nextColors.length) return false;
  for (let i = 0; i < prevColors.length; i++) {
    if (prevColors[i].color !== nextColors[i].color || prevColors[i].name !== nextColors[i].name)
      return false;
  }
  return true;
}

function SharedListDots({ colors }: { colors: SharedListColor[] }) {
  if (colors.length === 0) return null;
  return (
    <span
      className="inline-flex items-center gap-1 ml-1.5"
      title={colors.map((c) => c.name).join(', ')}
    >
      {colors.map((c) => (
        <span
          key={c.color}
          className="inline-block w-2 h-2 rounded-full flex-shrink-0"
          style={{ backgroundColor: c.color }}
        />
      ))}
    </span>
  );
}

export default memo(function FilmCard({
  screening,
  sectionLabel,
  venueDisplay,
  bookmarked,
  onToggleWatchlist,
  onOpenModal,
  sharedListColors,
}: Props) {
  const movie = screening.movie;
  const shareUrl = `${window.location.origin}${window.location.pathname}?f=${movie.slug}`;
  const colors = sharedListColors && sharedListColors.length > 0 ? sharedListColors : null;

  return (
    <div onClick={onOpenModal} className="block group cursor-pointer">
      {/* Desktop: vertical card */}
      <div className="hidden lg:flex flex-col bg-bars-bg-card rounded-bars-md overflow-hidden h-[280px]">
        {/* Thumbnail */}
        <div className="relative h-[140px] flex-shrink-0 overflow-hidden bg-bars-bg-medium">
          {movie.thumbnail ? (
            <Thumbnail html={movie.thumbnail} />
          ) : (
            <div className="w-full h-full bg-bars-bg-medium" />
          )}
          <div className="absolute top-2 right-2 flex flex-col gap-1.5">
            <BookmarkButton active={bookmarked} onClick={onToggleWatchlist} />
            <ShareButton
              url={shareUrl}
              title={movie.title}
              tooltipPosition="below"
              tooltipAlign="right"
            />
          </div>
        </div>

        {/* Content */}
        <div className="flex flex-col flex-1 p-3 gap-1.5 min-h-0">
          <div className="flex items-center gap-1">
            <span className="inline-flex self-start rounded-bars-sm bg-bars-primary-light px-1.5 py-0.5 text-[10px] font-semibold tracking-[1px] uppercase text-[#D4726A]">
              {sectionLabel}
            </span>
            {colors && <SharedListDots colors={colors} />}
          </div>
          {venueDisplay && (
            <span className="flex items-center gap-1 text-[11px] text-bars-text-subtle truncate">
              <MapPinIcon size={12} className="flex-shrink-0" />
              {venueDisplay}
            </span>
          )}
          <h4 className="font-heading text-xl font-semibold leading-[1.2] text-white line-clamp-2 group-hover:text-bars-badge-text transition-colors">
            {movie.title}
          </h4>
          {movie.info && (
            <span className="text-xs text-bars-text-subtle mt-auto truncate">{movie.info}</span>
          )}
        </div>
      </div>

      {/* Mobile: horizontal card */}
      <div className="flex lg:hidden bg-bars-bg-card rounded-bars-md overflow-hidden h-[110px]">
        {/* Thumbnail */}
        <div className="relative w-[130px] flex-shrink-0 overflow-hidden bg-bars-bg-medium">
          {movie.thumbnail ? (
            <Thumbnail html={movie.thumbnail} />
          ) : (
            <div className="w-full h-full bg-bars-bg-medium" />
          )}
          <div className="absolute top-1.5 right-1.5 flex flex-col gap-1">
            <BookmarkButton active={bookmarked} onClick={onToggleWatchlist} />
            <ShareButton
              url={shareUrl}
              title={movie.title}
              tooltipPosition="below"
              tooltipAlign="right"
            />
          </div>
        </div>

        {/* Content */}
        <div className="flex flex-col flex-1 p-2.5 gap-1 min-w-0">
          <div className="flex items-center gap-1">
            <span className="self-start max-w-full truncate rounded-bars-sm bg-bars-primary-light px-1.5 py-0.5 text-[8px] font-semibold tracking-[0.5px] uppercase text-[#D4726A]">
              {sectionLabel}
            </span>
            {colors && <SharedListDots colors={colors} />}
          </div>
          {venueDisplay && (
            <span className="flex items-center gap-1 text-[9px] text-bars-text-subtle truncate">
              <MapPinIcon size={10} className="flex-shrink-0" />
              {venueDisplay}
            </span>
          )}
          <h4 className="font-heading text-base font-semibold leading-[1.2] text-white line-clamp-2 group-hover:text-bars-badge-text transition-colors">
            {movie.title}
          </h4>
          {movie.info && (
            <span className="text-[10px] text-bars-text-subtle mt-auto truncate">{movie.info}</span>
          )}
        </div>
      </div>
    </div>
  );
}, arePropsEqual);
