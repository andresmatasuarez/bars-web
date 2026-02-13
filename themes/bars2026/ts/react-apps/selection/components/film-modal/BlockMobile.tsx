import { SingleEdition } from '@shared/ts/selection/Editions';
import { Movie, MovieSections } from '@shared/ts/selection/types';

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
    <>
      <BackHeader onClose={onClose} />
      <div className="overflow-y-auto flex-1">
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
                <ShortFilmCard key={s.id} short={s} />
              ))}
            </div>
          )}
          {/* Screenings */}
          {movie.screenings.length > 0 && (
            <div className="flex flex-col gap-3">
              <h3 className="font-heading text-lg font-medium text-white">
                Funciones
              </h3>
              {movie.screenings.map((s) => (
                <ScreeningCard key={s.raw} screening={s} currentEdition={currentEdition} compact />
              ))}
            </div>
          )}
        </div>
      </div>
    </>
  );
}
