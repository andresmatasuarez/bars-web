import { Movie } from '@shared/ts/selection/types';
import { memo } from 'react';

import BookmarkButton from '../BookmarkButton';

const HeroBackground = memo(function HeroBackground({ html }: { html: string }) {
  return (
    <div
      className="w-full h-full [&_img]:w-full [&_img]:h-full [&_img]:object-cover [&_img]:object-top [&_img]:grayscale-50 [&_img]:brightness-[0.4]"
      dangerouslySetInnerHTML={{ __html: html }}
    />
  );
});

export default function HeroSection({
  movie,
  sectionLabel,
  bookmarked,
  onToggleBookmark,
  height,
  titleSize,
  metaSize,
  badgeSize,
  padX,
  padY,
}: {
  movie: Movie;
  sectionLabel: string;
  bookmarked: boolean;
  onToggleBookmark: (() => void) | null;
  height: string;
  titleSize: string;
  metaSize: string;
  badgeSize: string;
  padX: number;
  padY: number;
}) {
  return (
    <div className={`relative w-full ${height} shrink-0`}>
      {/* Background thumbnail */}
      <div className="absolute inset-0 overflow-hidden">
        {movie.thumbnail ? (
          <HeroBackground html={movie.thumbnail} />
        ) : (
          <div className="w-full h-full bg-bars-bg-medium" />
        )}
      </div>
      {/* Gradient overlay */}
      <div
        className="absolute inset-0"
        style={{
          background:
            'linear-gradient(to bottom, rgba(10,10,10,0) 0%, rgba(10,10,10,0) 50%, rgba(10,10,10,0.6) 75%, #0A0A0A 100%)',
        }}
      />
      {/* Content */}
      <div
        className="absolute flex flex-col gap-2 lg:gap-3 z-10"
        style={{ left: padX, bottom: padY, right: padX }}
      >
        {/* Category badge */}
        <span
          className={`inline-flex self-start rounded-bars-sm px-2.5 py-1 ${badgeSize} font-semibold tracking-[1px] uppercase text-[#D4726A] bg-[rgba(139,0,0,0.27)]`}
        >
          {sectionLabel}
        </span>
        {/* Title + bookmark */}
        <div className="flex items-center gap-4">
          <h2 id="film-modal-title" className={`font-heading ${titleSize} font-semibold text-white leading-[1.1]`}>
            {movie.title}
          </h2>
          {onToggleBookmark && (
            <BookmarkButton
              active={bookmarked}
              onClick={onToggleBookmark}
              size="md"
            />
          )}
        </div>
        {/* Meta line */}
        {movie.info && (
          <span className={`${metaSize} text-white/60`}>{movie.info}</span>
        )}
      </div>
    </div>
  );
}
