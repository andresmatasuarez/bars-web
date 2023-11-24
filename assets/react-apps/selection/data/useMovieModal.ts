import { useCallback, useState } from 'react';

import { Movie } from '../types';

export type UseModalValues = {
  isOpen: boolean;
  open: (movie: Movie) => void;
  close: () => void;
  movieToDisplay: Movie | null;
};

export default function useMovieModal(): UseModalValues {
  const [isOpen, setIsOpen] = useState(false);
  const [movieToDisplay, setMovieToDisplay] = useState<Movie | null>(null);

  const open = useCallback<UseModalValues['open']>(
    (movie) => {
      setIsOpen(true);
      setMovieToDisplay(movie);
    },
    [setIsOpen],
  );

  const close = useCallback<UseModalValues['close']>(() => {
    setIsOpen(false);
    setMovieToDisplay(null);
  }, [setIsOpen]);

  return { isOpen, open, close, movieToDisplay };
}
