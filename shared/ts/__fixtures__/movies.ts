import type {
  AlwaysAvailableStreamingScreening,
  Movie,
  RegularStreamingScreening,
  ScreeningWithMovie,
  TraditionalScreening,
} from '../types';

export function createTraditionalScreening(
  overrides: Partial<TraditionalScreening> = {},
): TraditionalScreening {
  return {
    raw: 'belgrano.Sala 6:11-20-2025 20:00',
    venue: 'belgrano',
    room: 'Sala 6',
    isoDate: '2025-11-20T03:00:00-03:00',
    time: '20:00',
    ...overrides,
  };
}

export function createAlwaysAvailableStreaming(
  overrides: Partial<AlwaysAvailableStreamingScreening> = {},
): AlwaysAvailableStreamingScreening {
  return {
    raw: 'streaming!flixxo:full',
    venue: 'flixxo',
    streaming: true,
    alwaysAvailable: true,
    ...overrides,
  };
}

export function createRegularStreaming(
  overrides: Partial<RegularStreamingScreening> = {},
): RegularStreamingScreening {
  return {
    raw: 'streaming!flixxo:11-20-2025',
    venue: 'flixxo',
    streaming: true,
    isoDate: '2025-11-20T03:00:00-03:00',
    ...overrides,
  };
}

let movieIdCounter = 1000;

export function createMovie(overrides: Partial<Movie> = {}): Movie {
  const id = overrides.id ?? movieIdCounter++;
  return {
    id,
    slug: `movie-${id}`,
    thumbnail: `/img/${id}.jpg`,
    info: '96 min.',
    permalink: `http://localhost:8082/?movie=${id}`,
    section: 'internationalFeatureFilmCompetition',
    title: `Test Movie ${id}`,
    screenings: [createTraditionalScreening()],
    isBlock: false,
    ...overrides,
  };
}

export function createScreeningWithMovie<T extends TraditionalScreening | RegularStreamingScreening | AlwaysAvailableStreamingScreening>(
  screening: T,
  movieOverrides: Partial<Movie> = {},
): ScreeningWithMovie<T> {
  const movie = createMovie({ ...movieOverrides, screenings: [screening] });
  return { ...screening, movie };
}

export function createMoviesForDay(date: string, count: number): Movie[] {
  const isoDate = new Date(date).toISOString().replace('Z', '-03:00');
  const month = new Date(date).getMonth() + 1;
  const day = new Date(date).getDate();
  const year = new Date(date).getFullYear();
  const dateStr = `${month}-${String(day).padStart(2, '0')}-${year}`;

  return Array.from({ length: count }, (_, i) => {
    const time = `${String(18 + i).padStart(2, '0')}:00`;
    return createMovie({
      screenings: [
        createTraditionalScreening({
          raw: `belgrano.Sala 6:${dateStr} ${time}`,
          isoDate,
          time,
        }),
      ],
    });
  });
}
