import { ShortFilm } from '@shared/ts/selection/types';

export function ShortFilmCard({ short: s }: { short: ShortFilm }) {
  return (
    <div className="rounded-[6px] bg-bars-bg-card overflow-hidden flex flex-col gap-3">
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
            className="text-[11px] leading-[1.5] text-white/47 mt-1 [&_p]:m-0 [&_a]:text-bars-link-accent [&_a]:no-underline [&_a:hover]:underline [&_a]:transition-opacity"
            dangerouslySetInnerHTML={{ __html: s.synopsis }}
          />
        )}
      </div>
    </div>
  );
}

export function DesktopShortCard({ short: s }: { short: ShortFilm }) {
  return (
    <div className="rounded-bars-md bg-bars-bg-card overflow-hidden flex flex-col gap-3">
      {/* Thumbnail */}
      {s.thumbnail && (
        <div className="w-full h-[150px] flex-shrink-0 overflow-hidden rounded-t-bars-md">
          <div
            className="w-full h-full [&_img]:w-full [&_img]:h-full [&_img]:object-cover"
            dangerouslySetInnerHTML={{ __html: s.thumbnail }}
          />
        </div>
      )}
      {/* Info */}
      <div className="px-3 pb-3 pt-0 flex flex-col gap-2 flex-1">
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
      </div>
    </div>
  );
}
