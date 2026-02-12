import { Movie } from '@shared/ts/selection/types';
import { useCallback, useState } from 'react';

export type UseFilmModalValues = {
  selectedMovie: Movie | null;
  openFilmModal: (movie: Movie) => void;
  closeFilmModal: () => void;
};

export default function useFilmModal(): UseFilmModalValues {
  const [selectedMovie, setSelectedMovie] = useState<Movie | null>(null);
  const openFilmModal = useCallback((movie: Movie) => setSelectedMovie(movie), []);
  const closeFilmModal = useCallback(() => setSelectedMovie(null), []);
  return { selectedMovie, openFilmModal, closeFilmModal };
}
