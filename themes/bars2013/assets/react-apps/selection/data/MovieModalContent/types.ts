export type MovieAdditionalData = {
  id: number;

  /**
   * HTML string
   * @example "<img width="220" height="129" src="http://localhost:8082/wp-content/uploads/2019/11/You-might-be-the-killer-220x129.png" class="attachment-movie-post-image size-movie-post-image wp-post-image" alt="" srcset="http://localhost:8082/wp-content/uploads/2019/11/You-might-be-the-killer-220x129.png 220w, http://localhost:8082/wp-content/uploads/2019/11/You-might-be-the-killer-110x65.png 110w" sizes="(max-width: 220px) 100vw, 220px" />"
   */
  image: string;

  /**
   * Year of release
   */
  year?: string;

  /**
   * Country of origin
   */
  country?: string;

  /**
   * Runtime
   */
  runtime?: string;

  /**
   * Name of the directors of the movie, comma-separated.
   */
  directors?: string;

  /**
   * Names of the actors and actresses, comma-separated.
   */
  cast?: string;

  /**
   * Movie synopsis
   */
  synopsis?: string;

  /**
   * Comments, reviews, opinions, etc. about the movie
   */
  comments?: string;

  /**
   * URL of the teaser/trailer
   */
  trailerUrl?: string;

  /**
   * Link to stream the movie
   */
  streamingLink?: string;

  /**
   * Movie's title
   */
  title: string;

  /**
   * Movie's section
   */
  section: string;
};

export type MovieBlockAdditionalData = Pick<
  MovieAdditionalData,
  'id' | 'title' | 'section' | 'image' | 'runtime' | 'streamingLink'
> & {
  movies: (Omit<MovieAdditionalData, 'streamingLink' | 'section'> & {
    /**
     * Movie's thumbnail
     */
    thumbnail: string;

    /**
     * Movie's of a movie block don't have sections of their own.
     */
    section?: never;
  })[];
};

export function isMovieBlockAdditionalData(
  data: MovieAdditionalData | MovieBlockAdditionalData,
): data is MovieBlockAdditionalData {
  return 'movies' in data;
}

export function isRegularMovieAdditionalData(
  data: MovieAdditionalData | MovieBlockAdditionalData,
): data is MovieAdditionalData {
  return !isMovieBlockAdditionalData(data);
}
