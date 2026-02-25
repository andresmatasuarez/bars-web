import { Movies, MovieSections } from './types';

declare global {
  interface Window {
    MOVIES: Movies;
    MOVIE_SECTIONS: MovieSections;
    CURRENT_EDITION: number;
    LATEST_EDITION: number;
    BASE_PAGE_TITLE: string;
  }
}
