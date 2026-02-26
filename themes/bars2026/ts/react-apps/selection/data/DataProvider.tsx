import Editions, { SingleEdition } from '@shared/ts/Editions';
import { dateHasPassed, isDateBetween, serializeDate } from '@shared/ts/helpers';
import {
  DayGroup,
  getAlwaysAvailableScreenings,
  getScreeningsForDay,
  groupByDayAndTimeSlot,
  groupByTimeSlot,
} from '@shared/ts/screeningHelpers';
import {
  AlwaysAvailableStreamingScreening,
  Movie,
  Movies,
  MovieSections,
  RegularStreamingScreening,
  ScreeningWithMovie,
  TraditionalScreening,
  Venues,
} from '@shared/ts/types';
import useWatchlist, { serializeScreeningForWatchlist, UseWatchlistValues, WatchlistEntry } from '@shared/ts/useWatchlist';
import { createContext, ReactNode, useCallback, useContext, useEffect, useMemo, useState } from 'react';

import { decodeShareableList, SHARE_PARAM } from './shareableList';
import useFilmModal from './useFilmModal';
import useSharedLists, { SharedList } from './useSharedLists';

export type ActiveTab =
  | { type: 'day'; date: Date }
  | { type: 'online' }
  | { type: 'all' }
  | { type: 'watchlist' };

type DataContextType = {
  currentEdition: SingleEdition;
  movies: Movies;
  sections: MovieSections;
  venues: Venues;
  festivalDays: Date[];
  daysWithMovies: Date[];
  hasOnlineMovies: boolean;

  activeTab: ActiveTab;
  setActiveTab: (tab: ActiveTab) => void;

  activeCategories: string[];
  setActiveCategories: (categories: string[]) => void;
  toggleCategory: (category: string) => void;

  watchlistOnly: boolean;
  setWatchlistOnly: (value: boolean) => void;
  watchlistCountForTab: number;

  availableSections: MovieSections;
  sectionMovieCounts: Map<string, number>;

  sharedListMovieCountsForTab: Map<string, number>;

  screeningsForActiveTab: Map<
    string,
    ScreeningWithMovie<TraditionalScreening | RegularStreamingScreening>[]
  >;
  dayGroups: DayGroup[];
  alwaysAvailableScreenings: ScreeningWithMovie<AlwaysAvailableStreamingScreening>[];

  isAddedToWatchlist: UseWatchlistValues['isAddedToWatchlist'];
  addToWatchlist: UseWatchlistValues['addToWatchlist'];
  removeFromWatchlist: UseWatchlistValues['removeFromWatchlist'];
  toggleWatchlist: (screening: ScreeningWithMovie) => void;
  watchlist: WatchlistEntry[];
  resolvedWatchlist: WatchlistEntry[];

  sharedLists: SharedList[];
  editionSharedLists: SharedList[];
  emptySharedListIds: Set<string>;
  activeSharedListIds: string[];
  toggleSharedList: (id: string) => void;
  requestDeleteSharedList: (id: string) => void;
  getSharedListIdsForScreening: (screening: ScreeningWithMovie) => string[];

  deleteConfirmation: { id: string; name: string } | null;
  confirmDeleteSharedList: () => void;
  cancelDeleteSharedList: () => void;

  listSubTab: 'personal' | string;
  setListSubTab: (tab: 'personal' | string) => void;
  isInActiveSubTabList: (screening: ScreeningWithMovie) => boolean;
  activeListMovieCount: number;

  watchlistListFilters: string[];
  toggleWatchlistListFilter: (id: string) => void;
  watchlistOverlapCounts: Map<string, number>;

  pendingSharedList: { name: string; entries: WatchlistEntry[] } | null;
  replaceDialogOpen: boolean;
  handleReplace: (removeId: string) => void;
  cancelReplace: () => void;

  overwriteDialogOpen: boolean;
  overwriteTargetName: string;
  confirmOverwrite: () => void;
  cancelOverwrite: () => void;

  saveDialogOpen: boolean;
  openSaveDialog: () => void;
  closeSaveDialog: () => void;
  savePersonalAsShared: (name: string) => void;

  selectedMovie: Movie | null;
  openFilmModal: (movie: Movie) => void;
  closeFilmModal: () => void;
};

const DataContext = createContext<DataContextType | null>(null);

export function useData(): DataContextType {
  const ctx = useContext(DataContext);
  if (!ctx) throw new Error('useData must be used within DataProvider');
  return ctx;
}

function getInitialTab(days: Date[]): ActiveTab {
  if (days.length === 0) return { type: 'online' };

  const lastDay = days[days.length - 1];

  // Festival has passed: show all
  if (dateHasPassed(lastDay)) return { type: 'all' };

  const now = new Date();
  const from = days[0];

  if (isDateBetween(now, from, lastDay)) {
    // Find today
    const today = days.find((d) => {
      const dStr = serializeDate(d).split('T')[0];
      const nowStr = serializeDate(now).split('T')[0];
      return dStr === nowStr;
    });
    if (today) return { type: 'day', date: today };
  }

  // Before festival: first day
  return { type: 'day', date: days[0] };
}

/** Resolve shared list entries against current-edition screenings only, dropping stale entries. */
function resolveEntries(entries: WatchlistEntry[], festivalDays: Date[]): WatchlistEntry[] {
  const validEntries = new Set<string>();
  for (const day of festivalDays) {
    for (const s of getScreeningsForDay(window.MOVIES, day)) {
      validEntries.add(`${s.movie.id}_[${s.raw}]`);
    }
  }
  for (const s of getAlwaysAvailableScreenings(window.MOVIES)) {
    validEntries.add(`${s.movie.id}_[${s.raw}]`);
  }
  return entries.filter((entry) => validEntries.has(entry));
}

export default function DataProvider({ children }: { children: ReactNode }) {
  const currentEdition = useMemo(
    () => Editions.getByNumber(window.CURRENT_EDITION),
    [],
  );

  const festivalDays = useMemo(() => {
    try {
      return Editions.days(currentEdition);
    } catch {
      return [];
    }
  }, [currentEdition]);

  const daysWithMovies = useMemo(
    () => festivalDays.filter(date => getScreeningsForDay(window.MOVIES, date).length > 0),
    [festivalDays],
  );

  const hasOnlineMovies = useMemo(
    () => getAlwaysAvailableScreenings(window.MOVIES).length > 0,
    [],
  );

  const venues = useMemo(() => Editions.venues(currentEdition), [currentEdition]);

  const [activeTab, setActiveTab] = useState<ActiveTab>(() => getInitialTab(daysWithMovies));
  const [activeCategories, setActiveCategories] = useState<string[]>([]);
  const [watchlistOnly, setWatchlistOnly] = useState(false);

  const { watchlist, isAddedToWatchlist, addToWatchlist, removeFromWatchlist } = useWatchlist();

  // Watchlist entries filtered to current edition only (for sharing URLs)
  const resolvedWatchlist = useMemo(
    () => resolveEntries(watchlist, festivalDays),
    [watchlist, festivalDays],
  );

  const toggleWatchlist = useCallback(
    (screening: ScreeningWithMovie) => {
      if (isAddedToWatchlist(screening)) {
        removeFromWatchlist(screening);
      } else {
        addToWatchlist(screening);
      }
    },
    [isAddedToWatchlist, addToWatchlist, removeFromWatchlist],
  );

  const toggleCategory = useCallback(
    (category: string) => {
      setActiveCategories((prev) =>
        prev.includes(category)
          ? prev.filter((c) => c !== category)
          : [...prev, category],
      );
    },
    [],
  );

  const [listSubTab, setListSubTab] = useState<'personal' | string>('personal');

  const [watchlistListFilters, setWatchlistListFilters] = useState<string[]>([]);

  const toggleWatchlistListFilter = useCallback((id: string) => {
    setWatchlistListFilters(prev =>
      prev.includes(id) ? prev.filter(i => i !== id) : [...prev, id],
    );
  }, []);

  const { selectedMovie, openFilmModal, closeFilmModal } = useFilmModal();

  // --- Shared lists ---
  const {
    sharedLists: allSharedLists,
    addSharedList,
    removeSharedList: removeSharedListFromStorage,
    replaceSharedList: replaceSharedListInStorage,
    getSharedListIdsForScreening,
  } = useSharedLists();

  // Filter shared lists to the current edition (old untagged lists show everywhere for backward compat)
  const editionSharedLists = useMemo(
    () => allSharedLists.filter(
      (l) => l.edition === window.CURRENT_EDITION || !l.edition,
    ),
    [allSharedLists],
  );

  // Resolve entries to the current edition and hide lists with zero matches
  const sharedLists = useMemo(
    () =>
      editionSharedLists
        .map((list) => ({ ...list, entries: resolveEntries(list.entries, festivalDays) }))
        .filter((list) => list.entries.length > 0),
    [editionSharedLists, festivalDays],
  );

  // IDs of shared lists with zero movies for the current edition (for UI hints in replace dialog)
  const emptySharedListIds = useMemo(
    () => new Set(editionSharedLists.filter((l) => resolveEntries(l.entries, festivalDays).length === 0).map((l) => l.id)),
    [editionSharedLists, festivalDays],
  );

  const [activeSharedListIds, setActiveSharedListIds] = useState<string[]>([]);

  const toggleSharedList = useCallback((id: string) => {
    setActiveSharedListIds((prev) =>
      prev.includes(id) ? prev.filter((i) => i !== id) : [...prev, id],
    );
  }, []);

  const removeSharedList = useCallback(
    (id: string) => {
      removeSharedListFromStorage(id);
      setActiveSharedListIds((prev) => prev.filter((i) => i !== id));
    },
    [removeSharedListFromStorage],
  );

  // --- Delete confirmation ---
  const [deleteConfirmation, setDeleteConfirmation] = useState<{ id: string; name: string } | null>(null);

  const requestDeleteSharedList = useCallback(
    (id: string) => {
      const list = sharedLists.find((l) => l.id === id);
      if (list) setDeleteConfirmation({ id, name: list.name });
    },
    [sharedLists],
  );

  const confirmDeleteSharedList = useCallback(() => {
    if (deleteConfirmation) {
      removeSharedList(deleteConfirmation.id);
      setDeleteConfirmation(null);
    }
  }, [deleteConfirmation, removeSharedList]);

  const cancelDeleteSharedList = useCallback(() => {
    setDeleteConfirmation(null);
  }, []);

  // --- Replace dialog (when at capacity) ---
  const [pendingSharedList, setPendingSharedList] = useState<{ name: string; entries: WatchlistEntry[] } | null>(null);
  const [replaceDialogOpen, setReplaceDialogOpen] = useState(false);

  const handleReplace = useCallback(
    (removeId: string) => {
      if (!pendingSharedList) return;
      const newId = replaceSharedListInStorage(removeId, pendingSharedList.name, pendingSharedList.entries, window.CURRENT_EDITION);
      // Deactivate the removed list, activate the new one
      setActiveSharedListIds((prev) => [...prev.filter((i) => i !== removeId), newId]);
      // If we were viewing the removed list's sub-tab, switch to the new one
      if (listSubTab === removeId) setListSubTab(newId);
      setPendingSharedList(null);
      setReplaceDialogOpen(false);
    },
    [pendingSharedList, replaceSharedListInStorage, listSubTab],
  );

  const cancelReplace = useCallback(() => {
    setPendingSharedList(null);
    setReplaceDialogOpen(false);
  }, []);

  // --- Overwrite dialog (when a same-name list already exists) ---
  const [overwriteDialogOpen, setOverwriteDialogOpen] = useState(false);
  const [overwriteTargetId, setOverwriteTargetId] = useState<string | null>(null);

  const overwriteTargetName = pendingSharedList?.name ?? '';

  const confirmOverwrite = useCallback(() => {
    if (!pendingSharedList || !overwriteTargetId) return;
    const newId = replaceSharedListInStorage(overwriteTargetId, pendingSharedList.name, pendingSharedList.entries, window.CURRENT_EDITION);
    setActiveSharedListIds(prev => [...prev.filter(i => i !== overwriteTargetId), newId]);
    if (listSubTab === overwriteTargetId) setListSubTab(newId);
    setActiveTab({ type: 'watchlist' });
    setListSubTab(newId);
    setPendingSharedList(null);
    setOverwriteDialogOpen(false);
    setOverwriteTargetId(null);
  }, [pendingSharedList, overwriteTargetId, replaceSharedListInStorage, listSubTab]);

  const cancelOverwrite = useCallback(() => {
    setPendingSharedList(null);
    setOverwriteDialogOpen(false);
    setOverwriteTargetId(null);
  }, []);

  // --- Save personal watchlist as shared list ---
  const [saveDialogOpen, setSaveDialogOpen] = useState(false);

  const openSaveDialog = useCallback(() => setSaveDialogOpen(true), []);
  const closeSaveDialog = useCallback(() => setSaveDialogOpen(false), []);

  const savePersonalAsShared = useCallback(
    (name: string) => {
      // Only save entries that resolve to the current edition's festival days
      const resolved = resolveEntries(watchlist, festivalDays);
      if (resolved.length === 0) return;

      // Check if a list with the same name already exists in this edition → overwrite flow
      const existing = editionSharedLists.find(l => l.name === name);
      if (existing) {
        setPendingSharedList({ name, entries: resolved });
        setOverwriteTargetId(existing.id);
        setOverwriteDialogOpen(true);
        return;
      }

      // Try to add (per-edition capacity enforced in useSharedLists)
      const id = addSharedList(name, resolved, window.CURRENT_EDITION);
      if (id) {
        // Success — activate and navigate
        setActiveSharedListIds(prev => prev.includes(id) ? prev : [...prev, id]);
        setActiveTab({ type: 'watchlist' });
        setListSubTab(id);
      } else {
        // At capacity — open replace dialog
        setPendingSharedList({ name, entries: resolved });
        setReplaceDialogOpen(true);
      }
    },
    [editionSharedLists, watchlist, addSharedList, festivalDays],
  );

  // Clean up active IDs that no longer exist in sharedLists
  useEffect(() => {
    const ids = new Set(sharedLists.map((l) => l.id));
    setActiveSharedListIds((prev) => {
      const filtered = prev.filter((id) => ids.has(id));
      return filtered.length === prev.length ? prev : filtered;
    });
    // Reset listSubTab if the active shared list was deleted
    if (listSubTab !== 'personal' && !ids.has(listSubTab)) {
      setListSubTab('personal');
    }
  }, [sharedLists, listSubTab]);

  // Reset list overlap filters when sub-tab changes
  useEffect(() => {
    setWatchlistListFilters([]);
  }, [listSubTab]);

  // Handle ?list= URL param on mount
  useEffect(() => {
    const params = new URLSearchParams(window.location.search);
    const encoded = params.get(SHARE_PARAM);
    if (!encoded) return;

    const decoded = decodeShareableList(encoded);
    if (!decoded) {
      // Invalid payload — clean URL silently
      params.delete(SHARE_PARAM);
      const qs = params.toString();
      history.replaceState(null, '', window.location.pathname + (qs ? '?' + qs : ''));
      return;
    }

    const resolved = resolveEntries(decoded.entries, festivalDays);
    if (resolved.length === 0) {
      params.delete(SHARE_PARAM);
      const qs = params.toString();
      history.replaceState(null, '', window.location.pathname + (qs ? '?' + qs : ''));
      return;
    }

    // Clean URL first (preserve other params)
    params.delete(SHARE_PARAM);
    const qs = params.toString();
    history.replaceState(null, '', window.location.pathname + (qs ? '?' + qs : ''));

    // Check if a list with the same name already exists in this edition → ask before overwriting
    const existing = editionSharedLists.find(l => l.name === decoded.name);
    if (existing) {
      setPendingSharedList({ name: decoded.name, entries: resolved });
      setOverwriteTargetId(existing.id);
      setOverwriteDialogOpen(true);
    } else {
      const id = addSharedList(decoded.name, resolved, window.CURRENT_EDITION);
      if (id) {
        // Success — activate the new list and navigate to its sub-tab
        setActiveSharedListIds((prev) => prev.includes(id) ? prev : [...prev, id]);
        setActiveTab({ type: 'watchlist' });
        setListSubTab(id);
      } else {
        // At capacity — store pending and open replace dialog
        setPendingSharedList({ name: decoded.name, entries: resolved });
        setReplaceDialogOpen(true);
      }
    }
  }, []);

  // --- Shared list filtering helpers ---
  const activeSharedEntrySets = useMemo(() => {
    if (activeSharedListIds.length === 0) return null;
    const sets: Set<WatchlistEntry>[] = [];
    for (const list of sharedLists) {
      if (activeSharedListIds.includes(list.id)) {
        sets.push(new Set(list.entries));
      }
    }
    return sets.length > 0 ? sets : null;
  }, [activeSharedListIds, sharedLists]);

  // --- Sub-tab list membership check ---
  const activeSubTabEntrySet = useMemo(() => {
    if (listSubTab === 'personal') return null;
    const list = sharedLists.find((l) => l.id === listSubTab);
    return list ? new Set(list.entries) : null;
  }, [listSubTab, sharedLists]);

  const isInActiveSubTabList = useCallback(
    (screening: ScreeningWithMovie): boolean => {
      if (listSubTab === 'personal') return isAddedToWatchlist(screening);
      if (!activeSubTabEntrySet) return false;
      const entry = serializeScreeningForWatchlist(screening);
      return activeSubTabEntrySet.has(entry);
    },
    [listSubTab, isAddedToWatchlist, activeSubTabEntrySet],
  );

  // Screenings for the current tab before watchlist-only filter
  const rawTabScreenings = useMemo(() => {
    const movies = window.MOVIES;

    let screenings: ScreeningWithMovie<
      TraditionalScreening | RegularStreamingScreening
    >[] = [];

    if (activeTab.type === 'day') {
      screenings = getScreeningsForDay(movies, activeTab.date);
    } else if (activeTab.type === 'online') {
      screenings = getAlwaysAvailableScreenings(movies) as unknown as ScreeningWithMovie<
        TraditionalScreening | RegularStreamingScreening
      >[];
    } else if (activeTab.type === 'all') {
      for (const day of festivalDays) {
        screenings = screenings.concat(getScreeningsForDay(movies, day));
      }
    } else if (activeTab.type === 'watchlist') {
      for (const day of festivalDays) {
        screenings = screenings.concat(getScreeningsForDay(movies, day));
      }
      screenings = screenings.filter((s) => isInActiveSubTabList(s));
    }

    return screenings;
  }, [activeTab, festivalDays, isInActiveSubTabList]);

  // Apply watchlist-only + shared list filters + list overlap filters
  const baseScreenings = useMemo(() => {
    let screenings = rawTabScreenings;
    if (watchlistOnly && activeTab.type !== 'watchlist') {
      screenings = screenings.filter((s) => isAddedToWatchlist(s));
    }
    if (activeSharedEntrySets && activeTab.type !== 'watchlist') {
      for (const entrySet of activeSharedEntrySets) {
        screenings = screenings.filter(s => entrySet.has(serializeScreeningForWatchlist(s)));
      }
    }
    // List overlap filters (watchlist tab only): keep only movies present in ALL selected lists
    if (activeTab.type === 'watchlist' && watchlistListFilters.length > 0) {
      for (const filterId of watchlistListFilters) {
        const entrySet = new Set(
          filterId === 'personal'
            ? watchlist
            : sharedLists.find(l => l.id === filterId)?.entries ?? [],
        );
        screenings = screenings.filter(s => entrySet.has(serializeScreeningForWatchlist(s)));
      }
    }
    return screenings;
  }, [rawTabScreenings, watchlistOnly, activeTab.type, isAddedToWatchlist, activeSharedEntrySets, watchlistListFilters, watchlist, sharedLists]);

  // Count of unique watchlisted movies for the current tab (stable across watchlistOnly toggles)
  const watchlistCountForTab = useMemo(() => {
    if (activeTab.type === 'watchlist' || activeTab.type === 'all') {
      const watchlistedDay = rawTabScreenings.filter((s) =>
        activeTab.type === 'watchlist' || isAddedToWatchlist(s),
      );
      const dayCount = new Set(watchlistedDay.map((s) => s.movie.id)).size;
      const streamingCount = new Set(
        getAlwaysAvailableScreenings(window.MOVIES)
          .filter((s) => isAddedToWatchlist(s))
          .map((s) => s.movie.id),
      ).size;
      return dayCount + streamingCount;
    }
    const ids = new Set<number>();
    for (const s of rawTabScreenings) {
      if (isAddedToWatchlist(s)) ids.add(s.movie.id);
    }
    return ids.size;
  }, [rawTabScreenings, activeTab.type, isAddedToWatchlist]);

  // Count unique movies for the active sub-tab list (personal or shared), current edition only
  const activeListMovieCount = useMemo(() => {
    const currentMovieIds = new Set(window.MOVIES.map((m) => m.id));
    if (listSubTab === 'personal') {
      const ids = new Set<number>();
      for (const entry of watchlist) {
        const match = entry.match(/^(\d+)_/);
        if (match) {
          const movieId = Number(match[1]);
          if (currentMovieIds.has(movieId)) ids.add(movieId);
        }
      }
      return ids.size;
    }
    const list = sharedLists.find((l) => l.id === listSubTab);
    if (!list) return 0;
    const ids = new Set<number>();
    for (const entry of list.entries) {
      const match = entry.match(/^(\d+)_/);
      if (match) {
        const movieId = Number(match[1]);
        if (currentMovieIds.has(movieId)) ids.add(movieId);
      }
    }
    return ids.size;
  }, [listSubTab, watchlist, sharedLists]);

  // Overlap counts: how many movies in the active sub-tab also appear in each OTHER list
  const watchlistOverlapCounts = useMemo(() => {
    if (activeTab.type !== 'watchlist') return new Map<string, number>();
    const activeMovieIds = new Set(rawTabScreenings.map(s => s.movie.id));
    const counts = new Map<string, number>();

    // Personal watchlist overlap (shown when viewing a shared sub-tab)
    if (listSubTab !== 'personal') {
      const personalMovieIds = new Set<number>();
      for (const entry of watchlist) {
        const match = entry.match(/^(\d+)_/);
        if (match && activeMovieIds.has(Number(match[1]))) personalMovieIds.add(Number(match[1]));
      }
      counts.set('personal', personalMovieIds.size);
    }

    // Shared list overlaps (skip the active sub-tab's list)
    for (const list of sharedLists) {
      if (list.id === listSubTab) continue;
      const overlapIds = new Set<number>();
      for (const entry of list.entries) {
        const match = entry.match(/^(\d+)_/);
        if (match && activeMovieIds.has(Number(match[1]))) overlapIds.add(Number(match[1]));
      }
      counts.set(list.id, overlapIds.size);
    }
    return counts;
  }, [activeTab.type, rawTabScreenings, listSubTab, watchlist, sharedLists]);

  const sharedListMovieCountsForTab = useMemo(() => {
    const tabMovieIds = new Set(rawTabScreenings.map((s) => s.movie.id));
    if (activeTab.type === 'watchlist' || activeTab.type === 'all') {
      for (const s of getAlwaysAvailableScreenings(window.MOVIES)) {
        if (activeTab.type === 'all' || isAddedToWatchlist(s)) tabMovieIds.add(s.movie.id);
      }
    }
    const counts = new Map<string, number>();
    for (const list of sharedLists) {
      const movieIds = new Set(
        list.entries
          .map((e) => e.match(/^(\d+)_/)?.[1])
          .filter((id): id is string => id != null && tabMovieIds.has(Number(id)))
          .map(Number),
      );
      counts.set(list.id, movieIds.size);
    }
    return counts;
  }, [rawTabScreenings, activeTab.type, isAddedToWatchlist, sharedLists]);

  const availableSections = useMemo(
    (): MovieSections => {
      const sectionIds = new Set(baseScreenings.map((s) => s.movie.section));
      // For watchlist/all tabs, also include sections from always-available screenings
      if (activeTab.type === 'watchlist' || activeTab.type === 'all') {
        for (const s of getAlwaysAvailableScreenings(window.MOVIES)) {
          if (activeTab.type === 'all' || isInActiveSubTabList(s)) sectionIds.add(s.movie.section);
        }
      }
      return Object.fromEntries(
        Object.entries(window.MOVIE_SECTIONS).filter(([id]) => sectionIds.has(id)),
      ) as MovieSections;
    },
    [baseScreenings, activeTab.type, isInActiveSubTabList],
  );

  const sectionMovieCounts = useMemo(() => {
    const sectionSets = new Map<string, Set<number>>();
    for (const s of baseScreenings) {
      let ids = sectionSets.get(s.movie.section);
      if (!ids) { ids = new Set(); sectionSets.set(s.movie.section, ids); }
      ids.add(s.movie.id);
    }
    if (activeTab.type === 'watchlist' || activeTab.type === 'all') {
      for (const s of getAlwaysAvailableScreenings(window.MOVIES)) {
        if (activeTab.type === 'all' || isInActiveSubTabList(s)) {
          let ids = sectionSets.get(s.movie.section);
          if (!ids) { ids = new Set(); sectionSets.set(s.movie.section, ids); }
          ids.add(s.movie.id);
        }
      }
    }
    const counts = new Map<string, number>();
    sectionSets.forEach((ids, section) => counts.set(section, ids.size));
    return counts;
  }, [baseScreenings, activeTab.type, isInActiveSubTabList]);

  // Auto-reset categories when they're not available in the current tab
  useEffect(() => {
    if (activeCategories.length > 0) {
      const valid = activeCategories.filter((c) => c in availableSections);
      if (valid.length !== activeCategories.length) {
        setActiveCategories(valid);
      }
    }
  }, [activeCategories, availableSections]);

  const screeningsForActiveTab = useMemo(() => {
    let screenings = baseScreenings;
    if (activeCategories.length > 0) {
      screenings = screenings.filter((s) => activeCategories.includes(s.movie.section));
    }
    return groupByTimeSlot(screenings);
  }, [baseScreenings, activeCategories]);

  // Watchlist/all tabs: group day-based screenings by day then time slot
  const dayGroups = useMemo((): DayGroup[] => {
    if (activeTab.type !== 'watchlist' && activeTab.type !== 'all') return [];
    let screenings = baseScreenings;
    if (activeCategories.length > 0) {
      screenings = screenings.filter((s) => activeCategories.includes(s.movie.section));
    }
    return groupByDayAndTimeSlot(screenings);
  }, [activeTab.type, baseScreenings, activeCategories]);

  const alwaysAvailableScreenings = useMemo(() => {
    const all = getAlwaysAvailableScreenings(window.MOVIES);
    if (activeCategories.length > 0) {
      return all.filter((s) => activeCategories.includes(s.movie.section));
    }
    return all;
  }, [activeCategories]);

  const contextValue = useMemo(
    (): DataContextType => ({
      currentEdition,
      movies: window.MOVIES,
      sections: window.MOVIE_SECTIONS,
      venues,
      festivalDays,
      daysWithMovies,
      hasOnlineMovies,
      activeTab,
      setActiveTab,
      activeCategories,
      setActiveCategories,
      toggleCategory,
      watchlistOnly,
      setWatchlistOnly,
      watchlistCountForTab,
      availableSections,
      sectionMovieCounts,
      sharedListMovieCountsForTab,
      screeningsForActiveTab,
      dayGroups,
      alwaysAvailableScreenings,
      isAddedToWatchlist,
      addToWatchlist,
      removeFromWatchlist,
      toggleWatchlist,
      watchlist,
      resolvedWatchlist,
      sharedLists,
      editionSharedLists,
      emptySharedListIds,
      activeSharedListIds,
      toggleSharedList,
      requestDeleteSharedList,
      getSharedListIdsForScreening,
      listSubTab,
      setListSubTab,
      isInActiveSubTabList,
      activeListMovieCount,
      watchlistListFilters,
      toggleWatchlistListFilter,
      watchlistOverlapCounts,
      deleteConfirmation,
      confirmDeleteSharedList,
      cancelDeleteSharedList,
      pendingSharedList,
      replaceDialogOpen,
      handleReplace,
      cancelReplace,
      overwriteDialogOpen,
      overwriteTargetName,
      confirmOverwrite,
      cancelOverwrite,
      saveDialogOpen,
      openSaveDialog,
      closeSaveDialog,
      savePersonalAsShared,
      selectedMovie,
      openFilmModal,
      closeFilmModal,
    }),
    [
      currentEdition,
      venues,
      festivalDays,
      daysWithMovies,
      hasOnlineMovies,
      activeTab,
      activeCategories,
      toggleCategory,
      watchlistOnly,
      watchlistCountForTab,
      availableSections,
      sectionMovieCounts,
      sharedListMovieCountsForTab,
      screeningsForActiveTab,
      dayGroups,
      alwaysAvailableScreenings,
      isAddedToWatchlist,
      addToWatchlist,
      removeFromWatchlist,
      toggleWatchlist,
      watchlist,
      resolvedWatchlist,
      sharedLists,
      editionSharedLists,
      emptySharedListIds,
      activeSharedListIds,
      toggleSharedList,
      requestDeleteSharedList,
      getSharedListIdsForScreening,
      listSubTab,
      isInActiveSubTabList,
      activeListMovieCount,
      watchlistListFilters,
      toggleWatchlistListFilter,
      watchlistOverlapCounts,
      deleteConfirmation,
      confirmDeleteSharedList,
      cancelDeleteSharedList,
      pendingSharedList,
      replaceDialogOpen,
      handleReplace,
      cancelReplace,
      overwriteDialogOpen,
      overwriteTargetName,
      confirmOverwrite,
      cancelOverwrite,
      saveDialogOpen,
      openSaveDialog,
      closeSaveDialog,
      savePersonalAsShared,
      selectedMovie,
      openFilmModal,
      closeFilmModal,
    ],
  );

  return <DataContext.Provider value={contextValue}>{children}</DataContext.Provider>;
}
