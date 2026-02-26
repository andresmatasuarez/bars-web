import { SingleEdition } from '@shared/ts/Editions';
import { compareScreenings } from '@shared/ts/screeningHelpers';
import { Movie, MovieSections } from '@shared/ts/types';

import BackHeader from './BackHeader';
import HeroSection from './HeroSection';
import ScreeningCard from './ScreeningCard';
import { ShortFilmCard } from './ShortFilmCard';

export default function BlockMobile({
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
  const shorts = movie.shorts ?? [];
  const blockMeta = `${shorts.length} cortometraje${shorts.length !== 1 ? 's' : ''} • ${movie.info}`;

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
          metaOverride={blockMeta}
          movieSlug={movie.slug}
        />

        {/* Content */}
        <div className="flex flex-col gap-6 px-5 pt-6 pb-[85px]">
          {/* Shorts list */}
          {shorts.length > 0 && (
            <div className="flex flex-col gap-3">
              <h3 className="font-heading text-lg font-medium text-white">
                Cortometrajes incluidos
              </h3>
              {shorts.map((s) => (
                <ShortFilmCard
                  key={s.id}
                  short={s}
                  screenings={movie.screenings}
                  currentEdition={currentEdition}
                />
              ))}
            </div>
          )}
          {/* Screenings */}
          {movie.screenings.length > 0 && (
            <div className="flex flex-col gap-3">
              <h3 className="font-heading text-lg font-medium text-white">Funciones</h3>
              {[...movie.screenings].sort(compareScreenings).map((s) => (
                <ScreeningCard
                  key={s.raw}
                  screening={s}
                  streamingLink={movie.streamingLink}
                  currentEdition={currentEdition}
                  compact
                />
              ))}
            </div>
          )}
        </div>
      </div>
      <BackHeader onClose={onClose} />
    </div>
  );
}
