import { memo } from 'react';

export default memo(function TrailerEmbed({
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
      className={`block relative ${height} shrink-0 rounded-bars-md overflow-hidden group/trailer`}
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
});
