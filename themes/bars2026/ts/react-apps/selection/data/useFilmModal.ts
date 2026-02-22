import { Movie } from '@shared/ts/selection/types';
import { useCallback, useEffect, useState } from 'react';

export type UseFilmModalValues = {
  selectedMovie: Movie | null;
  openFilmModal: (movie: Movie) => void;
  closeFilmModal: () => void;
};

const MOVIE_PARAM = 'f';

function findMovieBySlug(slug: string): Movie | null {
  return window.MOVIES.find((m) => m.slug === slug) ?? null;
}

function getSlugFromUrl(): string | null {
  return new URLSearchParams(window.location.search).get(MOVIE_PARAM);
}

function buildUrl(slug: string | null): string {
  const params = new URLSearchParams(window.location.search);
  if (slug) {
    params.set(MOVIE_PARAM, slug);
  } else {
    params.delete(MOVIE_PARAM);
  }
  const qs = params.toString();
  return window.location.pathname + (qs ? '?' + qs : '');
}

export default function useFilmModal(): UseFilmModalValues {
  const [selectedMovie, setSelectedMovie] = useState<Movie | null>(() => {
    const slug = getSlugFromUrl();
    return slug ? findMovieBySlug(slug) : null;
  });

  const openFilmModal = useCallback((movie: Movie) => {
    setSelectedMovie(movie);
    history.pushState(null, '', buildUrl(movie.slug));
  }, []);

  const closeFilmModal = useCallback(() => {
    setSelectedMovie(null);
    history.pushState(null, '', buildUrl(null));
  }, []);

  useEffect(() => {
    const onPopState = () => {
      const slug = getSlugFromUrl();
      setSelectedMovie(slug ? findMovieBySlug(slug) : null);
    };
    window.addEventListener('popstate', onPopState);
    return () => window.removeEventListener('popstate', onPopState);
  }, []);

  return { selectedMovie, openFilmModal, closeFilmModal };
}
