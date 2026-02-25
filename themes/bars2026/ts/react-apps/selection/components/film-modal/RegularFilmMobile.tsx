import { SingleEdition } from '@shared/ts/selection/Editions';
import { Movie, MovieSections } from '@shared/ts/selection/types';

import BackHeader from './BackHeader';
import HeroSection from './HeroSection';
import ScreeningCard from './ScreeningCard';
import TrailerEmbed from './TrailerEmbed';

export default function RegularFilmMobile({
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
    <div className="relative flex-1 min-h-0">
      <div className="absolute inset-0 overflow-y-auto pt-16">
        {/* Hero */}
        <HeroSection
          movie={movie}
          sectionLabel={sectionLabel}
          bookmarked={bookmarked}
          onToggleBookmark={onToggleBookmark}
          height="h-[280px]"
          titleSize="text-[32px]"
          metaSize="text-[13px]"
          badgeSize="text-[9px]"
          padX={20}
          padY={20}
          movieSlug={movie.slug}
        />

        {/* Content */}
        <div className="flex flex-col gap-6 px-5 pt-6 pb-[85px]">
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
              <h3 className="font-heading text-[22px] font-medium text-white">
                Sinopsis
              </h3>
              <div
                className="text-sm leading-[1.7] text-white/80 [&_p]:m-0 [&_a]:text-bars-link-accent [&_a]:no-underline [&_a:hover]:underline [&_a]:transition-opacity"
                dangerouslySetInnerHTML={{ __html: movie.synopsis }}
              />
              {movie.comments && (
                <div className="border-l-2 border-white/20 pl-3 text-sm leading-[1.7] text-white/60 italic mt-4">
                  {movie.comments}
                </div>
              )}
            </div>
          )}
          {/* Trailer */}
          {movie.trailerUrl && movie.thumbnail && (
            <div className="flex flex-col gap-3">
              <h3 className="font-heading text-[22px] font-medium text-white">
                Trailer
              </h3>
              <TrailerEmbed
                trailerUrl={movie.trailerUrl}
                thumbnail={movie.thumbnail}
                compact
              />
            </div>
          )}
          {/* Screenings */}
          {movie.screenings.length > 0 && (
            <div className="flex flex-col gap-3">
              <h3 className="font-heading text-[22px] font-medium text-white">
                Funciones
              </h3>
              {movie.screenings.map((s) => (
                <ScreeningCard key={s.raw} screening={s} streamingLink={movie.streamingLink} currentEdition={currentEdition} compact />
              ))}
            </div>
          )}
        </div>
      </div>
      <BackHeader onClose={onClose} />
    </div>
  );
}
