import { SingleEdition } from '@shared/ts/selection/Editions';
import { Screening, ShortFilm } from '@shared/ts/selection/types';
import { useRef } from 'react';

import { resolveShortVerState } from './helpers';
import useLetterboxCrop from './useLetterboxCrop';

type ShortFilmCardProps = {
  short: ShortFilm;
  screenings: Screening[];
  currentEdition: SingleEdition;
};

function VerButton({ short, screenings, currentEdition }: ShortFilmCardProps) {
  if (!short.streamingLink) return null;

  const { enabled, disabledCaption } = resolveShortVerState(screenings, currentEdition);

  if (enabled) {
    return (
      <div className="mt-2">
        <a
          href={short.streamingLink}
          target="_blank"
          rel="noopener noreferrer"
          onClick={(e) => e.stopPropagation()}
          className="inline-block rounded-[6px] bg-bars-primary py-1.5 px-3 text-[11px] font-semibold text-white hover:brightness-110 transition-all"
        >
          Ver
        </a>
      </div>
    );
  }

  return (
    <div className="mt-2 flex flex-col items-start gap-1">
      <span className="inline-block rounded-[6px] bg-bars-primary opacity-40 cursor-not-allowed py-1.5 px-3 text-[11px] font-semibold text-white">
        Ver
      </span>
      {disabledCaption && (
        <span className="text-[9px] text-white/40 leading-tight">
          {disabledCaption}
        </span>
      )}
    </div>
  );
}

export function ShortFilmCard({ short: s, screenings, currentEdition }: ShortFilmCardProps) {
  const thumbRef = useRef<HTMLDivElement>(null);
  useLetterboxCrop(thumbRef);

  return (
    <div className="rounded-[6px] bg-bars-bg-card overflow-hidden flex flex-col gap-3">
      {/* Thumbnail */}
      {s.thumbnail && (
        <div className="w-full h-[160px] overflow-hidden">
          <div
            ref={thumbRef}
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
            className="text-[11px] leading-[1.5] text-white/47 mt-1 [&_p]:m-0 [&_a]:text-bars-link-accent [&_a]:no-underline [&_a:hover]:underline [&_a]:transition-opacity"
            dangerouslySetInnerHTML={{ __html: s.synopsis }}
          />
        )}
        {s.comments && (
          <div className="border-l border-white/15 pl-2 text-[11px] leading-[1.5] text-white/35 italic mt-1">
            {s.comments}
          </div>
        )}
        <VerButton short={s} screenings={screenings} currentEdition={currentEdition} />
      </div>
    </div>
  );
}

export function DesktopShortCard({ short: s, screenings, currentEdition }: ShortFilmCardProps) {
  const thumbRef = useRef<HTMLDivElement>(null);
  useLetterboxCrop(thumbRef);

  return (
    <div className="rounded-bars-md bg-bars-bg-card overflow-hidden flex flex-col gap-3">
      {/* Thumbnail */}
      {s.thumbnail && (
        <div className="w-full h-[150px] flex-shrink-0 overflow-hidden rounded-t-bars-md">
          <div
            ref={thumbRef}
            className="w-full h-full [&_img]:w-full [&_img]:h-full [&_img]:object-cover"
            dangerouslySetInnerHTML={{ __html: s.thumbnail }}
          />
        </div>
      )}
      {/* Info */}
      <div className="px-3 pb-3 pt-0 first:pt-3 flex flex-col gap-2 flex-1">
        <h5 className="text-sm font-semibold text-white">{s.title}</h5>
        {s.info && (
          <span className="text-xs text-white/40">{s.info}</span>
        )}
        {s.directors && (
          <span className="text-xs text-white/40">Dir: {s.directors}</span>
        )}
        {s.synopsis && (
          <div
            className="text-[11px] leading-[1.4] text-white/27 mt-1 [&_p]:m-0 [&_a]:text-bars-link-accent [&_a]:no-underline [&_a:hover]:underline [&_a]:transition-opacity"
            dangerouslySetInnerHTML={{ __html: s.synopsis }}
          />
        )}
        {s.comments && (
          <div className="border-l border-white/15 pl-2 text-[11px] leading-[1.4] text-white/20 italic mt-1">
            {s.comments}
          </div>
        )}
        <VerButton short={s} screenings={screenings} currentEdition={currentEdition} />
      </div>
    </div>
  );
}
