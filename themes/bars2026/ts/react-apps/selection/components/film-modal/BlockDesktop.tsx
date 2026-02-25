import { compareScreenings } from '@shared/ts/selection/data/helpers';
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
  const blockMeta = `${shorts.length} cortometraje${shorts.length !== 1 ? 's' : ''} • ${movie.info}`;

  return (
    <>
      {/* Hero */}
      <HeroSection
        movie={movie}
        sectionLabel={sectionLabel}
        bookmarked={bookmarked}
        onToggleBookmark={onToggleBookmark}
        height="h-[260px]"
        titleSize="text-5xl"
        metaSize="text-sm"
        badgeSize="text-[10px]"
        padX={48}
        padY={120}
        bgHeight={480}
        textPosition="top"
        metaOverride={blockMeta}
        movieSlug={movie.slug}
      />

      {/* Close button */}
      <div className="absolute top-3 right-4 z-20">
        <CloseButton onClick={onClose} />
      </div>

      {/* Two-column content */}
      <div className="relative z-10 flex flex-1 min-h-0 pb-6">
        {/* Left column: screenings */}
        <div className="w-[460px] shrink-0 overflow-y-auto px-12 pt-6 pb-10 flex flex-col gap-3">
          <h3 className="font-heading text-2xl font-medium text-white">
            Funciones
          </h3>
          {[...movie.screenings].sort(compareScreenings).map((s) => (
            <ScreeningCard key={s.raw} screening={s} streamingLink={movie.streamingLink} currentEdition={currentEdition} />
          ))}
        </div>

        {/* Right column: shorts list */}
        <div className="flex-1 flex flex-col min-h-0">
          <h3 className="font-heading text-2xl font-medium text-white pt-6 pr-[60px]">
            Cortometrajes en este bloque
          </h3>
          <div className="overflow-y-auto flex-1 min-h-0 pr-3 mr-12 pb-10 mt-3">
            <div className="grid grid-cols-2 gap-3">
              {shorts.map((s) => (
                <DesktopShortCard key={s.id} short={s} screenings={movie.screenings} currentEdition={currentEdition} />
              ))}
            </div>
          </div>
        </div>
      </div>
    </>
  );
}
