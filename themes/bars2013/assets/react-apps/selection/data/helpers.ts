import { serializeDate } from '../../helpers';
import { DEFAULT_SECTION_ALL } from '../App/Filters/useSectionSelector';
import Editions, { SingleEdition } from '../Editions';
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

/**
 * TODO move to DataProvider?
 */
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
    if (!sectionId || sectionId === DEFAULT_SECTION_ALL.value) {
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
    // Beware `screening1` here isn't a `RegularStreamingScreening` but a
    // `ScreeningWithMovie<RegularStreamingScreening>`
    return -1;
  }

  if (isRegularStreamingScreening(screening2)) {
    // Beware `screening2` here isn't a `RegularStreamingScreening` but a
    // `ScreeningWithMovie<RegularStreamingScreening>`
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
