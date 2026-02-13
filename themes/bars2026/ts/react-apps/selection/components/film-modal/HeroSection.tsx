import { Movie } from '@shared/ts/selection/types';
import { memo, useRef } from 'react';

import BookmarkButton from '../BookmarkButton';
import useLetterboxCrop from './useLetterboxCrop';

const HeroBackground = memo(function HeroBackground({ html }: { html: string }) {
  const ref = useRef<HTMLDivElement>(null);
  useLetterboxCrop(ref);

  return (
    <div
      ref={ref}
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
  bgHeight,
  textPosition = 'bottom',
  metaOverride,
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
  bgHeight?: number;
  textPosition?: 'top' | 'bottom';
  metaOverride?: string;
}) {
  return (
    <div className={`relative w-full ${textPosition === 'bottom' ? height : ''} shrink-0`}>
      {/* Background thumbnail */}
      <div
        className={`absolute left-0 right-0 top-0 overflow-hidden ${bgHeight ? '' : 'bottom-0'}`}
        style={bgHeight ? { height: bgHeight } : undefined}
      >
        {movie.thumbnail ? (
          <HeroBackground html={movie.thumbnail} />
        ) : (
          <div className="w-full h-full bg-bars-bg-medium" />
        )}
      </div>
      {/* Gradient overlay */}
      <div
        className={`absolute left-0 right-0 top-0 ${bgHeight ? '' : 'bottom-0'}`}
        style={{
          ...(bgHeight ? { height: bgHeight } : {}),
          background:
            'linear-gradient(to bottom, rgba(10,10,10,0) 0%, rgba(10,10,10,0) 50%, rgba(10,10,10,0.6) 75%, #0A0A0A 100%)',
        }}
      />
      {/* Content */}
      <div
        className={`${textPosition === 'top' ? 'relative' : 'absolute'} flex flex-col gap-2 lg:gap-3 z-10`}
        style={textPosition === 'top'
          ? { paddingLeft: padX, paddingRight: padX, paddingTop: padY }
          : { left: padX, right: padX, bottom: padY }
        }
      >
        {/* Category badge */}
        <span
          className={`inline-flex self-start rounded-bars-sm px-2.5 py-1 ${badgeSize} font-semibold tracking-[1px] uppercase text-[#D4726A]`}
          style={{ backgroundColor: 'rgba(139, 0, 0, 0.27)' }}
        >
          {sectionLabel}
        </span>
        {/* Title + bookmark */}
        <div>
          <h2 id="film-modal-title" className={`inline font-heading ${titleSize} font-semibold text-white leading-[1.1]`}>
            {movie.title}
          </h2>
          {onToggleBookmark && (
            <BookmarkButton
              active={bookmarked}
              onClick={onToggleBookmark}
              size="md"
              className="inline-flex ml-3"
            />
          )}
        </div>
        {/* Meta line */}
        {(metaOverride || movie.info) && (
          <span className={`${metaSize} text-white/60`}>{metaOverride ?? movie.info}</span>
        )}
      </div>
    </div>
  );
}
