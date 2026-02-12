import { SingleEdition } from '@shared/ts/selection/Editions';
import { Movie, MovieSections } from '@shared/ts/selection/types';

import CloseButton from './CloseButton';
import HeroSection from './HeroSection';
import ScreeningCard from './ScreeningCard';
import { DesktopShortCard } from './ShortFilmCard';

export default function BlockDesktop({
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
        {/* Left column: screenings */}
        <div className="w-[460px] shrink-0 overflow-y-auto px-12 py-2 pb-10 flex flex-col gap-3">
          <h3 className="font-heading text-2xl font-medium text-white">
            Funciones
          </h3>
          {movie.screenings.map((s) => (
            <ScreeningCard key={s.raw} screening={s} currentEdition={currentEdition} />
          ))}
        </div>

        {/* Right column: shorts list */}
        <div className="flex-1 overflow-y-auto py-2 pr-12 pb-10 flex flex-col gap-3">
          <h3 className="font-heading text-2xl font-medium text-white">
            Cortometrajes en este bloque
          </h3>
          <div className="grid grid-cols-2 gap-3">
            {shorts.map((s) => (
              <DesktopShortCard key={s.id} short={s} />
            ))}
          </div>
        </div>
      </div>
    </>
  );
}
