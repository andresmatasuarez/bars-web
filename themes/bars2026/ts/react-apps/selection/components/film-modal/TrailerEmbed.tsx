import { memo, useMemo, useState } from 'react';

type VideoEmbed =
  | { provider: 'youtube'; id: string }
  | { provider: 'vimeo'; id: string; hash?: string }
  | null;

function parseVideoUrl(url: string): VideoEmbed {
  try {
    const trimmed = url.trim();
    const u = new URL(trimmed);

    if (
      u.hostname.includes('youtube.com') ||
      u.hostname.includes('youtube-nocookie.com')
    ) {
      const id =
        u.searchParams.get('v') ||
        u.pathname.split('/embed/')[1] ||
        u.pathname.split('/shorts/')[1] ||
        u.pathname.split('/live/')[1] ||
        u.pathname.split('/v/')[1];
      if (id) return { provider: 'youtube', id: id.split(/[/?&]/)[0] };
    }
    if (u.hostname === 'youtu.be') {
      const id = u.pathname.slice(1).split(/[/?&]/)[0];
      if (id) return { provider: 'youtube', id };
    }

    if (u.hostname.includes('vimeo.com')) {
      // player.vimeo.com/video/ID?h=HASH
      const hParam = u.searchParams.get('h') || undefined;
      const match = u.pathname.match(/\/(\d+)(?:\/([a-f0-9]+))?/);
      if (match) {
        const hash = hParam || match[2] || undefined;
        return { provider: 'vimeo', id: match[1], hash };
      }
    }
  } catch {
    /* invalid URL */
  }

  if (import.meta.env.DEV) {
    console.warn('[TrailerEmbed] Unrecognized trailer URL:', url);
  }

  return null;
}

function getEmbedUrl(video: NonNullable<VideoEmbed>): string {
  if (video.provider === 'youtube') {
    return `https://www.youtube-nocookie.com/embed/${video.id}?autoplay=1&rel=0`;
  }
  const hash = video.hash ? `&h=${video.hash}` : '';
  return `https://player.vimeo.com/video/${video.id}?autoplay=1${hash}`;
}

export default memo(function TrailerEmbed({
  trailerUrl,
  thumbnail,
  compact,
}: {
  trailerUrl: string;
  thumbnail: string;
  compact?: boolean;
}) {
  const [playing, setPlaying] = useState(false);
  const video = useMemo(() => parseVideoUrl(trailerUrl), [trailerUrl]);
  const height = compact ? 'h-[190px]' : 'h-[220px]';

  if (playing && video) {
    return (
      <div
        className={`relative ${height} shrink-0 rounded-bars-md overflow-hidden bg-black`}
      >
        <iframe
          src={getEmbedUrl(video)}
          className="absolute inset-0 w-full h-full"
          allow="autoplay; encrypted-media; picture-in-picture"
          allowFullScreen
        />
      </div>
    );
  }

  const handleClick = (e: React.MouseEvent) => {
    e.stopPropagation();
    if (video) {
      e.preventDefault();
      setPlaying(true);
    }
  };

  return (
    <a
      href={trailerUrl}
      target="_blank"
      rel="noopener noreferrer"
      onClick={handleClick}
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
