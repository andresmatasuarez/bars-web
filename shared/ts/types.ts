type BaseScreening = {
  /**
   * @example "streaming!vivamoscultura:full"
   * @example "belgrano.Sala 6:12-06-2021 20:00"
   */
  raw: string;

  /**
   * @example "belgrano"
   * @example "vivamoscultura"
   */
  venue: string;
};

export type BaseStreamingScreening = BaseScreening & {
  streaming: true;
};

/**
 * Streaming movie available throughout the entire festival duration.
 * Raw format: `streaming!venue:full`
 */
export type AlwaysAvailableStreamingScreening = BaseStreamingScreening & {
  alwaysAvailable: true;

  /** @deprecated uso `isoDate` */
  date?: null;

  isoDate?: null;
};

/**
 * Streaming movie available on a specific day only.
 * Raw format: `streaming!venue:mm-dd-yyyy`
 */
export type RegularStreamingScreening = BaseStreamingScreening & {
  alwaysAvailable?: false;

  /**
   * Date in ISO 8601 / ATOM format, always in America/Argentina/Buenos_Aires (GMT-3).
   * @example '2023-10-31T00:00:00-03:00'
   */
  isoDate: string;
};

export type StreamingScreening = AlwaysAvailableStreamingScreening | RegularStreamingScreening;

/**
 * In-person screening at a physical venue with a specific date and time.
 * Raw format: `venue.room:mm-dd-yyyy hh:mm`
 */
export type TraditionalScreening = BaseScreening & {
  streaming?: false;

  /** @example "sala 6" */
  room?: string;

  /**
   * Date in ISO 8601 / ATOM format, always in America/Argentina/Buenos_Aires (GMT-3).
   * @example '2023-10-31T00:00:00-03:00'
   */
  isoDate: string;

  /** Time in Buenos Aires (GMT-3), 24h format. @example "20:00" */
  time: string;

  /** URL to purchase tickets for this specific screening. @example "https://passline.com/eventos/pelicula-123" */
  ticketUrl?: string;
};

export type Screening = StreamingScreening | TraditionalScreening;

export function isStreamingScreening(screening: Screening): screening is StreamingScreening {
  return !!screening.streaming;
}

export function isTraditionalScreening(screening: Screening): screening is TraditionalScreening {
  return !screening.streaming;
}

export function isScreeningAlwaysAvailable(
  screening: Screening,
): screening is AlwaysAvailableStreamingScreening {
  return isStreamingScreening(screening) && !!screening.alwaysAvailable;
}

export function isRegularStreamingScreening(
  screening: Screening,
): screening is RegularStreamingScreening {
  return isStreamingScreening(screening) && !screening.alwaysAvailable;
}

export type ShortFilm = {
  id: number;
  title: string;
  info: string;
  thumbnail: string;
  heroThumbnail?: string;
  directors?: string;
  synopsis?: string;
  comments?: string;
  streamingLink?: string;
};

export type Movie = {
  /** Wordpress post ID */
  id: number;

  /** Wordpress post_name (URL slug) */
  slug: string;

  thumbnail: string;
  heroThumbnail?: string;

  /** @example "96 min." */
  info: string;

  /** @example "http://localhost:8082/?movieblock=bloque-1-8" */
  permalink: string;

  /** @example "shortFilmCompetition" */
  section: string;

  /** @example "BLOQUE 1" */
  title: string;

  screenings: Screening[];

  /** URL to stream the movie/block online (from `_movie_streamingLink` / `_movieblock_streamingLink` meta). */
  streamingLink?: string;

  isBlock: boolean;

  /** Only present for regular films (isBlock === false) */
  directors?: string;
  cast?: string;
  synopsis?: string;
  comments?: string;
  trailerUrl?: string;

  /** Only present for blocks (isBlock === true) */
  shorts?: ShortFilm[];
};

export type Movies = Movie[];

/**
 * @example {
 *   bizarreCompetition: "Competencia Bizarra",
 *   bloodyWeekend: "Fin de semana sangriento",
 *   iberoamericanFeatureFilmCompetition: "Competencia Iberoamericana",
 *   internationalFeatureFilmCompetition: "Competencia Internacional",
 *   japaneseInvasion: "Invasión Japón",
 *   laCripta: "La Cripta",
 *   releases: "Novedades",
 *   reposiciones: "Reposiciones",
 *   shortFilm: "Cortos fuera de competencia",
 *   shortFilmCompetition: "Cortos en competencia",
 *   specialScreenings: "Funciones especiales",
 * }
 */
export type MovieSections = Record<
  string, // Section ID
  string // Section label
>;

/**
 * @example {
 *   belgrano: {
 *     name: "Multiplex Belgrano",
 *     address: "Vuelta de Obligado 2199",
 *     link: "https://goo.gl/maps/j8JkEPEw72UDBisx5",
 *   },
 *   ccsm: {
 *     name: "C.C. San Martín",
 *     address: "Sarmiento 1551",
 *     link: "https://goo.gl/maps/8HhZmY9LNfQWV7YeA",
 *   },
 *   vivamoscultura: {
 *     name: "VivamosCultura",
 *     address: "vivamoscultura.buenosaires.gob.ar",
 *     online: true,
 *     link: "https://vivamoscultura.buenosaires.gob.ar/",
 *   },
 * }
 */
export type Venues = Partial<
  Record<
    string, // Venue ID
    {
      name: string;

      /** Street address */
      address: string;

      /** Link to the official site of the venue */
      link?: string;

      /** true if the venue is a streaming platform */
      online?: boolean;
    }
  >
>;

export type ScreeningWithMovie<S extends Screening = Screening> = S & {
  movie: Movie;
};

export type ScreeningsByDay = Record<
  /**
   * The date of the day in ISO format
   * @example '2023-10-31T21:17:29.889Z'
   */
  `${number}-${number}-${number}T${number}:${number}:${number}.${number}Z`,
  ScreeningWithMovie<TraditionalScreening | RegularStreamingScreening>[]
>;
