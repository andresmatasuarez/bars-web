import { useCallback, useEffect, useMemo, useRef, useState } from 'react';

import { ScreeningWithMovie } from '../types';

const LOCALSTORAGE_KEY = 'bars-watchlist';

/**
 * Within the context of a single edition, I think movie ID is not necessary
 * since the `raw` property of the screening is unique.
 *
 * But `screening.raw` might not be unique across festival editions. For instance,
 * two different movies in different editions may have the exact same screening.
 *
 * I need to be able tell them apart and that's why I need the movie post ID. I might
 * as well have used the edition number but the movie post ID was closer at hand and it's
 * unique across editions.
 */
type WatchlistEntry = `${/* Movie post ID */ number}_[${/* Screening `raw` value */ string}]`;

export function serializeScreeningForWatchlist(screening: ScreeningWithMovie): WatchlistEntry {
  return `${screening.movie.id}_[${screening.raw}]`;
}

function loadWatchlist(): WatchlistEntry[] {
  try {
    const persisted = localStorage.getItem(LOCALSTORAGE_KEY);
    if (!persisted) return [];
    // TODO Validate watchlist object with zod or something
    return JSON.parse(persisted);
  } catch {
    return [];
  }
}

export type UseWatchlistValues = {
  watchlist: WatchlistEntry[];
  isAddedToWatchlist: (screening: ScreeningWithMovie) => boolean;
  addToWatchlist: (screening: ScreeningWithMovie) => void;
  removeFromWatchlist: (screening: ScreeningWithMovie) => void;
};

export default function useWatchlist(): UseWatchlistValues {
  const [watchlist, setWatchlist] = useState<WatchlistEntry[]>(loadWatchlist);

  // Skip persisting on the initial render (the state already came from localStorage)
  const isInitialMount = useRef(true);
  useEffect(() => {
    if (isInitialMount.current) {
      isInitialMount.current = false;
      return;
    }
    localStorage.setItem(LOCALSTORAGE_KEY, JSON.stringify(watchlist));
  }, [watchlist]);

  const watchlistSet = useMemo(() => new Set(watchlist), [watchlist]);

  const addToWatchlist = useCallback<UseWatchlistValues['addToWatchlist']>((screening) => {
    const entry = serializeScreeningForWatchlist(screening);
    setWatchlist((prev) => (prev.includes(entry) ? prev : [...prev, entry]));
  }, []);

  const removeFromWatchlist = useCallback<UseWatchlistValues['removeFromWatchlist']>(
    (screening) => {
      const entryToRemove = serializeScreeningForWatchlist(screening);
      setWatchlist((prev) => prev.filter((entry) => entry !== entryToRemove));
    },
    [],
  );

  const isAddedToWatchlist = useCallback<UseWatchlistValues['isAddedToWatchlist']>(
    (screening) => watchlistSet.has(serializeScreeningForWatchlist(screening)),
    [watchlistSet],
  );

  return { watchlist, addToWatchlist, removeFromWatchlist, isAddedToWatchlist };
}
