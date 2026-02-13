import {
  DayGroup,
  getAlwaysAvailableScreenings,
  getScreeningsForDay,
  groupByDayAndTimeSlot,
  groupByTimeSlot,
} from '@shared/ts/selection/data/helpers';
import useWatchlist, { UseWatchlistValues } from '@shared/ts/selection/data/useWatchlist';
import Editions, { SingleEdition } from '@shared/ts/selection/Editions';
import { dateHasPassed, isDateBetween, serializeDate } from '@shared/ts/selection/helpers';
import {
  AlwaysAvailableStreamingScreening,
  Movie,
  Movies,
  MovieSections,
  RegularStreamingScreening,
  ScreeningWithMovie,
  TraditionalScreening,
  Venues,
} from '@shared/ts/selection/types';
import { createContext, ReactNode, useCallback, useContext, useEffect, useMemo, useState } from 'react';

import useFilmModal from './useFilmModal';

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

  activeCategory: string | null;
  setActiveCategory: (category: string | null) => void;

  watchlistOnly: boolean;
  setWatchlistOnly: (value: boolean) => void;
  watchlistCountForTab: number;

  availableSections: MovieSections;

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
  const [activeCategory, setActiveCategory] = useState<string | null>(null);
  const [watchlistOnly, setWatchlistOnly] = useState(false);

  const { watchlist, isAddedToWatchlist, addToWatchlist, removeFromWatchlist } = useWatchlist();

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

  const { selectedMovie, openFilmModal, closeFilmModal } = useFilmModal();

  // Screenings for the current tab before watchlist-only filter
  const rawTabScreenings = useMemo(() => {
    const movies = window.MOVIES;

    let screenings: ScreeningWithMovie<
      TraditionalScreening | RegularStreamingScreening
    >[] = [];

    if (activeTab.type === 'day') {
      screenings = getScreeningsForDay(movies, activeTab.date);
    } else if (activeTab.type === 'online') {
      screenings = getAlwaysAvailableScreenings(movies) as ScreeningWithMovie<
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
      screenings = screenings.filter((s) => isAddedToWatchlist(s));
    }

    return screenings;
  }, [activeTab, festivalDays, isAddedToWatchlist, watchlist]);

  // Apply watchlist-only filter
  const baseScreenings = useMemo(() => {
    if (watchlistOnly && activeTab.type !== 'watchlist') {
      return rawTabScreenings.filter((s) => isAddedToWatchlist(s));
    }
    return rawTabScreenings;
  }, [rawTabScreenings, watchlistOnly, activeTab.type, isAddedToWatchlist]);

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
    const ids = new Set<string>();
    for (const s of rawTabScreenings) {
      if (isAddedToWatchlist(s)) ids.add(s.movie.id);
    }
    return ids.size;
  }, [rawTabScreenings, activeTab.type, isAddedToWatchlist]);

  const availableSections = useMemo(
    (): MovieSections => {
      const sectionIds = new Set(baseScreenings.map((s) => s.movie.section));
      // For watchlist/all tabs, also include sections from always-available screenings
      if (activeTab.type === 'watchlist' || activeTab.type === 'all') {
        for (const s of getAlwaysAvailableScreenings(window.MOVIES)) {
          if (activeTab.type === 'all' || isAddedToWatchlist(s)) sectionIds.add(s.movie.section);
        }
      }
      return Object.fromEntries(
        Object.entries(window.MOVIE_SECTIONS).filter(([id]) => sectionIds.has(id)),
      ) as MovieSections;
    },
    [baseScreenings, activeTab.type, isAddedToWatchlist],
  );

  // Auto-reset category when it's not available in the current tab
  useEffect(() => {
    if (activeCategory && !(activeCategory in availableSections)) {
      setActiveCategory(null);
    }
  }, [activeCategory, availableSections]);

  const screeningsForActiveTab = useMemo(() => {
    let screenings = baseScreenings;
    if (activeCategory) {
      screenings = screenings.filter((s) => s.movie.section === activeCategory);
    }
    return groupByTimeSlot(screenings);
  }, [baseScreenings, activeCategory]);

  // Watchlist/all tabs: group day-based screenings by day then time slot
  const dayGroups = useMemo((): DayGroup[] => {
    if (activeTab.type !== 'watchlist' && activeTab.type !== 'all') return [];
    let screenings = baseScreenings;
    if (activeCategory) {
      screenings = screenings.filter((s) => s.movie.section === activeCategory);
    }
    return groupByDayAndTimeSlot(screenings);
  }, [activeTab.type, baseScreenings, activeCategory]);

  const alwaysAvailableScreenings = useMemo(() => {
    return getAlwaysAvailableScreenings(window.MOVIES, {
      section: activeCategory ?? undefined,
    });
  }, [activeCategory]);

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
      activeCategory,
      setActiveCategory,
      watchlistOnly,
      setWatchlistOnly,
      watchlistCountForTab,
      availableSections,
      screeningsForActiveTab,
      dayGroups,
      alwaysAvailableScreenings,
      isAddedToWatchlist,
      addToWatchlist,
      removeFromWatchlist,
      toggleWatchlist,
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
      activeCategory,
      watchlistOnly,
      watchlistCountForTab,
      availableSections,
      screeningsForActiveTab,
      dayGroups,
      alwaysAvailableScreenings,
      isAddedToWatchlist,
      addToWatchlist,
      removeFromWatchlist,
      toggleWatchlist,
      selectedMovie,
      openFilmModal,
      closeFilmModal,
    ],
  );

  return <DataContext.Provider value={contextValue}>{children}</DataContext.Provider>;
}
