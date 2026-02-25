import Editions from '@shared/ts/selection/Editions';
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

function buildDocumentTitle(movie: Movie): string {
  const sectionLabel = window.MOVIE_SECTIONS[movie.section] || '';
  const editionTitle = Editions.getTitle(Editions.getByNumber(window.CURRENT_EDITION));
  const suffix = sectionLabel ? `${editionTitle} - ${sectionLabel}` : editionTitle;
  const siteSuffix =
    window.BASE_PAGE_TITLE?.split(' \u2013 ').slice(1).join(' \u2013 ') || '';
  return `${movie.title} (${suffix}) \u2013 ${siteSuffix}`;
}

export default function useFilmModal(): UseFilmModalValues {
  const [selectedMovie, setSelectedMovie] = useState<Movie | null>(() => {
    const slug = getSlugFromUrl();
    return slug ? findMovieBySlug(slug) : null;
  });

  const openFilmModal = useCallback((movie: Movie) => {
    setSelectedMovie(movie);
    history.pushState(null, '', buildUrl(movie.slug));
    document.title = buildDocumentTitle(movie);
  }, []);

  const closeFilmModal = useCallback(() => {
    setSelectedMovie(null);
    history.pushState(null, '', buildUrl(null));
    document.title = window.BASE_PAGE_TITLE || document.title;
  }, []);

  useEffect(() => {
    const onPopState = () => {
      const slug = getSlugFromUrl();
      const movie = slug ? findMovieBySlug(slug) : null;
      setSelectedMovie(movie);
      document.title = movie
        ? buildDocumentTitle(movie)
        : window.BASE_PAGE_TITLE || document.title;
    };
    window.addEventListener('popstate', onPopState);
    return () => window.removeEventListener('popstate', onPopState);
  }, []);

  return { selectedMovie, openFilmModal, closeFilmModal };
}
