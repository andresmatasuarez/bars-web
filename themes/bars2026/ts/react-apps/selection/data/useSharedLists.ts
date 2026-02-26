import { ScreeningWithMovie } from '@shared/ts/types';
import { serializeScreeningForWatchlist, WatchlistEntry } from '@shared/ts/useWatchlist';
import { useCallback, useEffect, useMemo, useRef, useState } from 'react';

import { MAX_SHARED_LISTS } from './shareableList';

const LOCALSTORAGE_KEY = 'bars-shared-lists';

export type SharedList = {
  id: string;
  name: string;
  entries: WatchlistEntry[];
  addedAt: number;
  edition?: number;
};

type PersistedData = SharedList[];

function generateId(): string {
  return Date.now().toString(36) + Math.random().toString(36).slice(2, 6);
}

function loadSharedLists(): SharedList[] {
  try {
    const persisted = localStorage.getItem(LOCALSTORAGE_KEY);
    if (!persisted) return [];
    const parsed: unknown = JSON.parse(persisted);
    if (!Array.isArray(parsed)) return [];
    return (parsed as PersistedData).filter(
      (item) =>
        typeof item.id === 'string' &&
        typeof item.name === 'string' &&
        Array.isArray(item.entries) &&
        typeof item.addedAt === 'number',
    );
  } catch {
    return [];
  }
}

export type UseSharedListsValues = {
  sharedLists: SharedList[];
  addSharedList: (name: string, entries: WatchlistEntry[], edition: number) => string | null;
  removeSharedList: (id: string) => void;
  replaceSharedList: (removeId: string, name: string, entries: WatchlistEntry[], edition: number) => string;
  getSharedListIdsForScreening: (screening: ScreeningWithMovie) => string[];
};

export default function useSharedLists(): UseSharedListsValues {
  const [sharedLists, setSharedLists] = useState<SharedList[]>(loadSharedLists);

  // Skip persisting on initial render (state already came from localStorage)
  const isInitialMount = useRef(true);
  useEffect(() => {
    if (isInitialMount.current) {
      isInitialMount.current = false;
      return;
    }
    localStorage.setItem(LOCALSTORAGE_KEY, JSON.stringify(sharedLists));
  }, [sharedLists]);

  // Build Set-based lookup per list for O(1) screening checks
  const listEntrySets = useMemo(
    () =>
      new Map(
        sharedLists.map((list) => [list.id, new Set(list.entries)] as const),
      ),
    [sharedLists],
  );

  const addSharedList = useCallback(
    (name: string, entries: WatchlistEntry[], edition: number): string | null => {
      const id = generateId();
      let added = true;
      setSharedLists((prev) => {
        if (prev.filter((l) => l.edition === edition).length >= MAX_SHARED_LISTS) {
          added = false;
          return prev;
        }
        return [...prev, { id, name, entries, addedAt: Date.now(), edition }];
      });
      return added ? id : null;
    },
    [],
  );

  const replaceSharedList = useCallback(
    (removeId: string, name: string, entries: WatchlistEntry[], edition: number): string => {
      const id = generateId();
      setSharedLists((prev) => {
        const idx = prev.findIndex((l) => l.id === removeId);
        if (idx === -1) return [...prev, { id, name, entries, addedAt: Date.now(), edition }];
        const next = [...prev];
        next[idx] = { id, name, entries, addedAt: Date.now(), edition };
        return next;
      });
      return id;
    },
    [],
  );

  const removeSharedList = useCallback((id: string) => {
    setSharedLists((prev) => prev.filter((l) => l.id !== id));
  }, []);

  const getSharedListIdsForScreening = useCallback(
    (screening: ScreeningWithMovie): string[] => {
      const entry = serializeScreeningForWatchlist(screening);
      const ids: string[] = [];
      listEntrySets.forEach((entrySet, id) => {
        if (entrySet.has(entry)) ids.push(id);
      });
      return ids;
    },
    [listEntrySets],
  );

  return { sharedLists, addSharedList, removeSharedList, replaceSharedList, getSharedListIdsForScreening };
}
