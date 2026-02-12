import { SingleEdition } from '@shared/ts/selection/Editions';
import { Movie, MovieSections } from '@shared/ts/selection/types';

import CloseButton from './CloseButton';
import HeroSection from './HeroSection';
import ScreeningCard from './ScreeningCard';
import TrailerEmbed from './TrailerEmbed';

export default function RegularFilmDesktop({
  movie,
  onClose,
  currentEdition,
  sections,
  bookmarked,
  onToggleBookmark,
}: {
  movie: Movie;
  onClose: () => void;
  currentEdition: SingleEdition;
  sections: MovieSections;
  bookmarked: boolean;
  onToggleBookmark: (() => void) | null;
}) {
  const sectionLabel = sections[movie.section] ?? movie.section;

  return (
    <>
      {/* Hero */}
      <HeroSection
        movie={movie}
        sectionLabel={sectionLabel}
        bookmarked={bookmarked}
        onToggleBookmark={onToggleBookmark}
        height="h-[340px]"
        titleSize="text-5xl"
        metaSize="text-sm"
        badgeSize="text-[10px]"
        padX={48}
        padY={48}
      />

      {/* Close button */}
      <div className="absolute top-3 right-4 z-20">
        <CloseButton onClick={onClose} />
      </div>

      {/* Two-column content */}
      <div className="flex flex-1 min-h-0">
        {/* Left column: crew + synopsis */}
        <div className="flex-1 overflow-y-auto py-2 px-12 pb-10 flex flex-col gap-6">
          {/* Crew */}
          {(movie.directors || movie.cast) && (
            <div className="flex flex-col gap-3">
              {movie.directors && (
                <div className="flex gap-2">
                  <span className="text-sm text-white/40 shrink-0">Director:</span>
                  <span className="text-sm text-white">{movie.directors}</span>
                </div>
              )}
              {movie.cast && (
                <div className="flex gap-2">
                  <span className="text-sm text-white/40 shrink-0">Reparto:</span>
                  <span className="text-sm text-white leading-[1.5]">
                    {movie.cast}
                  </span>
                </div>
              )}
            </div>
          )}
          {/* Synopsis */}
          {movie.synopsis && (
            <div className="flex flex-col gap-3">
              <h3 className="font-heading text-2xl font-medium text-white">
                Sinopsis
              </h3>
              <div
                className="text-sm leading-[1.7] text-white/80 [&_p]:m-0 [&_a]:text-bars-link-accent [&_a]:no-underline [&_a:hover]:underline [&_a]:transition-opacity"
                dangerouslySetInnerHTML={{ __html: movie.synopsis }}
              />
            </div>
          )}
        </div>

        {/* Right column: trailer + screenings */}
        <div className="w-[454px] shrink-0 overflow-y-auto pr-12 py-2 pb-10 flex flex-col gap-6">
          {/* Trailer */}
          {movie.trailerUrl && movie.thumbnail && (
            <TrailerEmbed
              trailerUrl={movie.trailerUrl}
              thumbnail={movie.thumbnail}
            />
          )}
          {/* Screenings */}
          {movie.screenings.length > 0 && (
            <div className="flex flex-col gap-3">
              <h3 className="font-heading text-2xl font-medium text-white">
                Funciones
              </h3>
              {movie.screenings.map((s) => (
                <ScreeningCard key={s.raw} screening={s} currentEdition={currentEdition} />
              ))}
            </div>
          )}
        </div>
      </div>
    </>
  );
}
