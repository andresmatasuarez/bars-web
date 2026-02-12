import Editions from '@shared/ts/selection/Editions';
import {
  isTraditionalScreening,
  Movie,
  Screening,
  ShortFilm,
} from '@shared/ts/selection/types';

import { Modal } from '../../../components/modal/Modal';
import { useData } from '../data/DataProvider';
import BookmarkButton from './BookmarkButton';
import { MapPinIcon } from './icons';

// --- Shared sub-components ---

function CloseButton({ onClick }: { onClick: () => void }) {
  return (
    <button
      onClick={onClick}
      className="w-8 h-8 rounded-full flex items-center justify-center cursor-pointer transition-colors"
      style={{ backgroundColor: 'rgba(0, 0, 0, 0.4)' }}
      onMouseEnter={(e) =>
        (e.currentTarget.style.backgroundColor = 'rgba(0, 0, 0, 0.6)')
      }
      onMouseLeave={(e) =>
        (e.currentTarget.style.backgroundColor = 'rgba(0, 0, 0, 0.4)')
      }
      aria-label="Cerrar"
    >
      <svg
        width="16"
        height="16"
        viewBox="0 0 24 24"
        fill="none"
        stroke="white"
        strokeWidth="2"
        strokeLinecap="round"
        strokeLinejoin="round"
      >
        <line x1="18" y1="6" x2="6" y2="18" />
        <line x1="6" y1="6" x2="18" y2="18" />
      </svg>
    </button>
  );
}

function BackHeader({ onClose }: { onClose: () => void }) {
  return (
    <div
      className="flex items-center h-16 px-5 shrink-0"
      style={{ backgroundColor: 'rgba(10, 10, 10, 0.8)' }}
    >
      <button
        onClick={onClose}
        className="flex items-center gap-2 cursor-pointer"
        aria-label="Volver"
      >
        <svg
          width="20"
          height="20"
          viewBox="0 0 24 24"
          fill="none"
          stroke="white"
          strokeWidth="2"
          strokeLinecap="round"
          strokeLinejoin="round"
        >
          <polyline points="15 18 9 12 15 6" />
        </svg>
        <span className="text-sm font-medium text-white">Programación</span>
      </button>
    </div>
  );
}

function getSpanishDayAbbr(date: Date): string {
  const day = date.toLocaleDateString('es-AR', { weekday: 'short' });
  return day.replace('.', '').toUpperCase().slice(0, 3);
}

function ScreeningCard({
  screening,
  compact,
}: {
  screening: Screening;
  compact?: boolean;
}) {
  const { currentEdition } = useData();

  const date = screening.isoDate ? new Date(screening.isoDate) : null;
  let venueName = '';
  let roomName = '';
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

  if (isTraditionalScreening(screening) && screening.room) {
    roomName = screening.room;
  }

  const venueDisplay = roomName ? `${venueName} - ${roomName}` : venueName;
  const time = isTraditionalScreening(screening) ? screening.time : null;

  const dateBoxSize = compact ? 'h-11 w-10' : 'h-[52px] w-12';
  const dateBoxPadding = compact ? 'py-1.5 px-2.5' : 'py-2 px-3';
  const dayFontSize = compact ? 'text-[9px]' : 'text-[10px]';
  const numFontSize = compact ? 'text-xl' : 'text-2xl';
  const timeFontSize = compact ? 'text-lg' : 'text-lg';
  const venueFontSize = compact ? 'text-[11px]' : 'text-xs';
  const btnPadding = compact ? 'py-2 px-3.5' : 'py-2 px-4';
  const btnFontSize = compact ? 'text-[11px]' : 'text-xs';

  return (
    <div className="flex items-center justify-between rounded-bars-md bg-bars-bg-card p-3 gap-3">
      <div className="flex items-center gap-3">
        {/* Date box */}
        {date && (
          <div
            className={`flex flex-col items-center ${dateBoxSize} ${dateBoxPadding} rounded-[6px]`}
            style={{ backgroundColor: 'rgba(139, 0, 0, 0.2)' }}
          >
            <span
              className={`${dayFontSize} font-semibold tracking-[1px] text-white/60`}
            >
              {getSpanishDayAbbr(date)}
            </span>
            <span className={`font-display ${numFontSize} leading-none text-white`}>
              {date.getDate()}
            </span>
          </div>
        )}
        {/* Time + venue */}
        <div className="flex flex-col gap-0.5">
          {time && (
            <span className={`font-display ${timeFontSize} text-white`}>
              {time}
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
      {/* Tickets button */}
      {venueLink && (
        <a
          href={venueLink}
          target="_blank"
          rel="noopener noreferrer"
          onClick={(e) => e.stopPropagation()}
          className={`rounded-[6px] bg-bars-primary ${btnPadding} ${btnFontSize} font-semibold text-white hover:brightness-110 transition-all`}
        >
          Tickets
        </a>
      )}
    </div>
  );
}

function TrailerEmbed({
  trailerUrl,
  thumbnail,
  compact,
}: {
  trailerUrl: string;
  thumbnail: string;
  compact?: boolean;
}) {
  const height = compact ? 'h-[190px]' : 'h-[220px]';
  return (
    <a
      href={trailerUrl}
      target="_blank"
      rel="noopener noreferrer"
      onClick={(e) => e.stopPropagation()}
      className={`block relative ${height} rounded-bars-md overflow-hidden group/trailer`}
    >
      {thumbnail ? (
        <div
          className="w-full h-full [&_img]:w-full [&_img]:h-full [&_img]:object-cover"
          dangerouslySetInnerHTML={{ __html: thumbnail }}
        />
      ) : (
        <div className="w-full h-full bg-bars-bg-card" />
      )}
      <div className="absolute inset-0 bg-black/20 group-hover/trailer:bg-black/40 transition-colors" />
      {/* Play button */}
      <div className="absolute inset-0 flex items-center justify-center">
        <div className="w-16 h-16 rounded-full bg-bars-primary flex items-center justify-center">
          <svg
            width="28"
            height="28"
            viewBox="0 0 24 24"
            fill="white"
            className="ml-1"
          >
            <polygon points="5 3 19 12 5 21 5 3" />
          </svg>
        </div>
      </div>
    </a>
  );
}

function ShortFilmCard({ short: s }: { short: ShortFilm }) {
  return (
    <div className="rounded-[6px] bg-bars-bg-card overflow-hidden">
      {/* Thumbnail */}
      {s.thumbnail && (
        <div className="w-full h-[160px] overflow-hidden">
          <div
            className="w-full h-full [&_img]:w-full [&_img]:h-full [&_img]:object-cover"
            dangerouslySetInnerHTML={{ __html: s.thumbnail }}
          />
        </div>
      )}
      {/* Info */}
      <div className="p-3 flex flex-col gap-1">
        <h5 className="font-heading text-base font-semibold text-white">
          {s.title}
        </h5>
        {s.info && (
          <span className="text-[10px] text-white/40">{s.info}</span>
        )}
        {s.directors && (
          <span className="text-[10px] text-white/40">
            Dir: {s.directors}
          </span>
        )}
        {s.synopsis && (
          <div
            className="text-[11px] leading-[1.5] text-white/47 line-clamp-4 mt-1 [&_p]:m-0 [&_a]:text-bars-link-accent [&_a]:no-underline [&_a:hover]:underline [&_a]:transition-opacity"
            dangerouslySetInnerHTML={{ __html: s.synopsis }}
          />
        )}
      </div>
    </div>
  );
}

// --- Desktop short card (wider, for 2-column grid) ---

function DesktopShortCard({ short: s }: { short: ShortFilm }) {
  return (
    <div className="rounded-bars-md bg-bars-bg-card overflow-hidden flex flex-col h-[380px]">
      {/* Thumbnail */}
      {s.thumbnail && (
        <div className="w-full h-[120px] flex-shrink-0 overflow-hidden rounded-t-bars-md">
          <div
            className="w-full h-full [&_img]:w-full [&_img]:h-full [&_img]:object-cover"
            dangerouslySetInnerHTML={{ __html: s.thumbnail }}
          />
        </div>
      )}
      {/* Info */}
      <div className="px-3 pb-3 pt-0 flex flex-col gap-2 flex-1 overflow-hidden">
        <h5 className="text-sm font-semibold text-white">{s.title}</h5>
        {s.info && (
          <span className="text-xs text-white/40">{s.info}</span>
        )}
        {s.directors && (
          <span className="text-xs text-white/40">Dir: {s.directors}</span>
        )}
        {s.synopsis && (
          <div
            className="text-[11px] leading-[1.4] text-white/27 line-clamp-6 mt-1 [&_p]:m-0 [&_a]:text-bars-link-accent [&_a]:no-underline [&_a:hover]:underline [&_a]:transition-opacity"
            dangerouslySetInnerHTML={{ __html: s.synopsis }}
          />
        )}
      </div>
    </div>
  );
}

// --- Hero section (shared desktop/mobile) ---

function HeroSection({
  movie,
  height,
  titleSize,
  metaSize,
  badgeSize,
  padX,
  padY,
}: {
  movie: Movie;
  height: string;
  titleSize: string;
  metaSize: string;
  badgeSize: string;
  padX: number;
  padY: number;
}) {
  const { sections, isAddedToWatchlist, toggleWatchlist } = useData();
  const sectionLabel = sections[movie.section] ?? movie.section;

  // Find a screening to check bookmark status
  const firstScreening = movie.screenings[0];
  const bookmarked = firstScreening
    ? isAddedToWatchlist({ ...firstScreening, movie })
    : false;

  return (
    <div className={`relative w-full ${height} shrink-0`}>
      {/* Background thumbnail */}
      <div className="absolute inset-0 overflow-hidden">
        {movie.thumbnail ? (
          <div
            className="w-full h-full [&_img]:w-full [&_img]:h-full [&_img]:object-cover [&_img]:object-top [&_img]:grayscale-50 [&_img]:brightness-[0.4]"
            dangerouslySetInnerHTML={{ __html: movie.thumbnail }}
          />
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
          className={`inline-flex self-start rounded-bars-sm px-2.5 py-1 ${badgeSize} font-semibold tracking-[1px] uppercase text-[#D4726A]`}
          style={{ backgroundColor: 'rgba(139, 0, 0, 0.27)' }}
        >
          {sectionLabel}
        </span>
        {/* Title + bookmark */}
        <div className="flex items-center gap-4">
          <h2 className={`font-heading ${titleSize} font-semibold text-white leading-[1.1]`}>
            {movie.title}
          </h2>
          {firstScreening && (
            <BookmarkButton
              active={bookmarked}
              onClick={() => toggleWatchlist({ ...firstScreening, movie })}
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

// --- Regular Film: Desktop ---

function RegularFilmDesktop({
  movie,
  onClose,
}: {
  movie: Movie;
  onClose: () => void;
}) {
  return (
    <>
      {/* Hero */}
      <HeroSection
        movie={movie}
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
                <ScreeningCard key={s.raw} screening={s} />
              ))}
            </div>
          )}
        </div>
      </div>
    </>
  );
}

// --- Regular Film: Mobile ---

function RegularFilmMobile({
  movie,
  onClose,
}: {
  movie: Movie;
  onClose: () => void;
}) {
  return (
    <>
      <BackHeader onClose={onClose} />
      <div className="overflow-y-auto flex-1">
        {/* Hero */}
        <HeroSection
          movie={movie}
          height="h-[280px]"
          titleSize="text-[32px]"
          metaSize="text-[13px]"
          badgeSize="text-[9px]"
          padX={20}
          padY={20}
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
                <ScreeningCard key={s.raw} screening={s} compact />
              ))}
            </div>
          )}
        </div>
      </div>
    </>
  );
}

// --- Block: Desktop ---

function BlockDesktop({
  movie,
  onClose,
}: {
  movie: Movie;
  onClose: () => void;
}) {
  const shorts = movie.shorts ?? [];

  return (
    <>
      {/* Hero */}
      <HeroSection
        movie={movie}
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
            <ScreeningCard key={s.raw} screening={s} />
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

// --- Block: Mobile ---

function BlockMobile({
  movie,
  onClose,
}: {
  movie: Movie;
  onClose: () => void;
}) {
  const shorts = movie.shorts ?? [];

  return (
    <>
      <BackHeader onClose={onClose} />
      <div className="overflow-y-auto flex-1">
        {/* Hero */}
        <HeroSection
          movie={movie}
          height="h-[280px]"
          titleSize="text-[32px]"
          metaSize="text-[13px]"
          badgeSize="text-[9px]"
          padX={20}
          padY={20}
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
                <ScreeningCard key={s.raw} screening={s} compact />
              ))}
            </div>
          )}
        </div>
      </div>
    </>
  );
}

// --- Main FilmModal ---

export default function FilmModal() {
  const { selectedMovie, closeFilmModal } = useData();
  const movie = selectedMovie;

  const isBlock = movie?.isBlock ?? false;

  const containerClassName = isBlock
    ? 'relative w-full h-full lg:max-w-[1100px] lg:max-h-[900px] lg:rounded-bars-lg bg-bars-bg-dark flex flex-col overflow-hidden'
    : 'relative w-full h-full lg:max-w-[1058px] lg:max-h-[877px] lg:rounded-bars-lg bg-bars-bg-dark flex flex-col overflow-hidden';

  return (
    <Modal
      isOpen={movie !== null}
      onClose={closeFilmModal}
      containerClassName={containerClassName}
    >
      {movie && (
        <>
          {/* Desktop layout */}
          <div className="hidden lg:flex lg:flex-col lg:h-full lg:min-h-0">
            {isBlock ? (
              <BlockDesktop movie={movie} onClose={closeFilmModal} />
            ) : (
              <RegularFilmDesktop movie={movie} onClose={closeFilmModal} />
            )}
          </div>
          {/* Mobile layout */}
          <div className="flex flex-col h-full lg:hidden">
            {isBlock ? (
              <BlockMobile movie={movie} onClose={closeFilmModal} />
            ) : (
              <RegularFilmMobile movie={movie} onClose={closeFilmModal} />
            )}
          </div>
        </>
      )}
    </Modal>
  );
}
