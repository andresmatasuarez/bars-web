import Editions, { SingleEdition } from '../Editions';
import { serializeDate } from '../helpers';
import {
  AlwaysAvailableStreamingScreening,
  isRegularStreamingScreening,
  isScreeningAlwaysAvailable,
  isTraditionalScreening,
  Movies,
  RegularStreamingScreening,
  Screening,
  ScreeningWithMovie,
  TraditionalScreening,
} from '../types';

/** The value used to represent "all sections" (no filter). */
const ALL_SECTIONS_VALUE = 'all';

export function getCurrentEdition(): SingleEdition {
  const qs = new URLSearchParams(window.location.search);
  const rawEditionNumber = qs.get('edition');

  if (rawEditionNumber) {
    const currentEditionNumber = Number.parseInt(rawEditionNumber, 10);
    return Editions.getByNumber(currentEditionNumber);
  }

  return Editions.latest();
}

export function isLatestEdition(): boolean {
  return Editions.latest() === getCurrentEdition();
}

function isScreeningOfDay(
  screening: TraditionalScreening | RegularStreamingScreening,
  date: Date,
): boolean {
  const serializedDate = serializeDate(date);
  const day = serializedDate.split('T')[0];
  const screeningDay = screening.isoDate.split('T')[0];
  return day === screeningDay;
}

function filterBySection(sectionId?: string) {
  return function sectionFilterPredicate(screening: ScreeningWithMovie): boolean {
    if (!sectionId || sectionId === ALL_SECTIONS_VALUE) {
      return true;
    }
    return screening.movie.section === sectionId;
  };
}

function getScreenings<T extends Screening>(
  movies: Movies,
  filterFn: (screening: Screening) => screening is T,
): ScreeningWithMovie<T>[] {
  const moviesForDay = movies.filter((movie) => {
    return movie.screenings.some(filterFn);
  });

  const screeningsForDay = moviesForDay.map((movie) => {
    return movie.screenings
      .filter(filterFn)
      .map((screening): ScreeningWithMovie<T> => ({ ...screening, movie }));
  });

  // Flatten [][] into []
  return ([] as ScreeningWithMovie<T>[]).concat(...screeningsForDay);
}

type ScreeningFilters = {
  section?: string;
};

export function compareScreenings(
  screening1: TraditionalScreening | RegularStreamingScreening,
  screening2: TraditionalScreening | RegularStreamingScreening,
): number {
  /**
   * Regular streaming screenings do not have time information, so we want them
   * to appear first in the sorted array.
   */
  if (isRegularStreamingScreening(screening1)) {
    return -1;
  }

  if (isRegularStreamingScreening(screening2)) {
    return 1;
  }

  /**
   * Sort lexicographically by day/hour/minutes/seconds
   * https://stackoverflow.com/a/57589653
   */
  return (
    screening1.isoDate.localeCompare(screening2.isoDate) ||
    screening1.time.localeCompare(screening2.time)
  );
}

export function getScreeningsForDay(
  movies: Movies,
  date: Date,
  filters?: ScreeningFilters,
): ScreeningWithMovie<TraditionalScreening | RegularStreamingScreening>[] {
  return getScreenings(
    movies,
    (screening): screening is TraditionalScreening | RegularStreamingScreening => {
      if (isTraditionalScreening(screening)) {
        return isScreeningOfDay(screening, date);
      }
      return isRegularStreamingScreening(screening) && isScreeningOfDay(screening, date);
    },
  )
    .filter(filterBySection(filters?.section))
    .sort(compareScreenings);
}

export function getAlwaysAvailableScreenings(
  movies: Movies,
  filters?: ScreeningFilters,
): ScreeningWithMovie<AlwaysAvailableStreamingScreening>[] {
  return getScreenings(movies, isScreeningAlwaysAvailable).filter(
    filterBySection(filters?.section),
  );
}

export type DayGroup = {
  date: Date;
  timeSlots: Map<string, ScreeningWithMovie<TraditionalScreening | RegularStreamingScreening>[]>;
};

/** Group screenings first by day, then by time slot within each day. Days sorted chronologically. */
export function groupByDayAndTimeSlot(
  screenings: ScreeningWithMovie<TraditionalScreening | RegularStreamingScreening>[],
): DayGroup[] {
  const dayMap = new Map<
    string,
    { date: Date; screenings: ScreeningWithMovie<TraditionalScreening | RegularStreamingScreening>[] }
  >();

  for (const screening of screenings) {
    const dateKey = screening.isoDate.split('T')[0];
    const existing = dayMap.get(dateKey);
    if (existing) {
      existing.screenings.push(screening);
    } else {
      const [year, month, day] = dateKey.split('-').map(Number);
      dayMap.set(dateKey, { date: new Date(year, month - 1, day), screenings: [screening] });
    }
  }

  // Sort days chronologically and group each day's screenings by time slot
  return Array.from(dayMap.entries())
    .sort(([a], [b]) => a.localeCompare(b))
    .map(([, { date, screenings: dayScreenings }]) => ({
      date,
      timeSlots: groupByTimeSlot(dayScreenings),
    }));
}

/** Group screenings by time slot and sort chronologically */
export function groupByTimeSlot(
  screenings: ScreeningWithMovie<TraditionalScreening | RegularStreamingScreening>[],
): Map<string, ScreeningWithMovie<TraditionalScreening | RegularStreamingScreening>[]> {
  const groups = new Map<
    string,
    ScreeningWithMovie<TraditionalScreening | RegularStreamingScreening>[]
  >();

  for (const screening of screenings) {
    const time = isTraditionalScreening(screening) ? screening.time : 'Online';
    const existing = groups.get(time);
    if (existing) {
      existing.push(screening);
    } else {
      groups.set(time, [screening]);
    }
  }

  return groups;
}
