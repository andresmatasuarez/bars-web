import { useCallback, useEffect, useState } from "react";
import { ScreeningWithMovie } from "../types";

const LOCALSTORAGE_KEY = "bars-watchlist";

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
type WatchlistEntry = `${/* Movie post ID */ number}_[${
  /* Screening `raw` value */ string
}]`;

export function serializeScreeningForWatchlist(
  screening: ScreeningWithMovie
): WatchlistEntry {
  return `${screening.movie.id}_[${screening.raw}]`;
}

function removeDuplicateEntries(watchlist: WatchlistEntry[]): WatchlistEntry[] {
  return watchlist.filter(
    (watchlistEntry, index) => watchlist.indexOf(watchlistEntry) === index
  );
}

export type UseWatchlistValues = {
  watchlist: WatchlistEntry[];
  isAddedToWatchlist: (screening: ScreeningWithMovie) => boolean;
  addToWatchlist: (screening: ScreeningWithMovie) => void;
  removeFromWatchlist: (screening: ScreeningWithMovie) => void;
};

export default function useWatchlist(): UseWatchlistValues {
  const [watchlist, setWatchlist] = useState<WatchlistEntry[]>([]);

  useEffect(() => {
    const persistedWatchlist = localStorage.getItem(LOCALSTORAGE_KEY);

    if (!persistedWatchlist) {
      console.info("useWatchlist :: no existing watchlist data found.");
      return;
    }

    const watchlist: WatchlistEntry[] = JSON.parse(persistedWatchlist);

    // TODO Validate watchlist object with zod or something

    setWatchlist(watchlist);
  }, []);

  useEffect(() => {
    localStorage.setItem(LOCALSTORAGE_KEY, JSON.stringify(watchlist));
  }, [watchlist]);

  const addToWatchlist = useCallback<UseWatchlistValues["addToWatchlist"]>(
    (screening) => {
      setWatchlist((previousWatchlist) =>
        removeDuplicateEntries([
          ...previousWatchlist,
          serializeScreeningForWatchlist(screening),
        ])
      );
    },
    []
  );

  const removeFromWatchlist = useCallback<
    UseWatchlistValues["removeFromWatchlist"]
  >((screening) => {
    const entryToRemove = serializeScreeningForWatchlist(screening);

    setWatchlist((previousWatchlist) =>
      previousWatchlist.filter(
        (watchlistEntry) => watchlistEntry !== entryToRemove
      )
    );
  }, []);

  const isAddedToWatchlist = useCallback<
    UseWatchlistValues["isAddedToWatchlist"]
  >(
    (screening) => {
      return watchlist.includes(serializeScreeningForWatchlist(screening));
    },
    [watchlist]
  );

  return { watchlist, addToWatchlist, removeFromWatchlist, isAddedToWatchlist };
}
