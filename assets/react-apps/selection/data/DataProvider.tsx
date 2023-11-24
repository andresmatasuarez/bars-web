import { createContext, Dispatch, SetStateAction, useMemo, useState } from 'react';

import { serializeDate } from '../../helpers';
import useSectionSelector, {
  ChangeSectionHandler,
  SectionOption,
  SectionOptionShape,
} from '../App/Filters/useSectionSelector';
import Editions, { SingleEdition } from '../Editions';
import {
  AlwaysAvailableStreamingScreening,
  ExpectsChildren,
  Movies,
  MovieSections,
  ScreeningsByDay,
  ScreeningWithMovie,
} from '../types';
import { getAlwaysAvailableScreenings, getCurrentEdition, getScreeningsForDay } from './helpers';
import MovieModal from './MovieModal';
import MovieModalContent from './MovieModalContent';
import useModal, { UseModalValues } from './useMovieModal';
import useWatchlist, { UseWatchlistValues } from './useWatchlist';

export enum MovieListType {
  ALL = 'ALL',
  WATCHLIST = 'WATCHLIST',
}

type DataContextType = {
  currentEdition: SingleEdition;

  movies: Movies;
  sections: MovieSections;
  screeningsByDay: ScreeningsByDay;
  alwaysAvailableScreenings: ScreeningWithMovie<AlwaysAvailableStreamingScreening>[];
  selectedSection: SectionOption;
  sectionOptions: SectionOptionShape[];
  changeSection: ChangeSectionHandler;

  isAddedToWatchlist: UseWatchlistValues['isAddedToWatchlist'];
  addToWatchlist: UseWatchlistValues['addToWatchlist'];
  removeFromWatchlist: UseWatchlistValues['removeFromWatchlist'];

  currentMovieListType: MovieListType;
  changeMovieListType: Dispatch<SetStateAction<MovieListType>>;

  openMovieModal: UseModalValues['open'];
  closeMovieModal: UseModalValues['close'];
};

const defaultContext = {
  currentEdition: getCurrentEdition(),

  movies: window.MOVIES,
  sections: window.MOVIE_SECTIONS,
  screeningsByDay: {},
  alwaysAvailableScreenings: [],

  selectedSection: null,
  sectionOptions: [],
  changeSection: () => {
    throw new Error("`changeSection` in DataContext hasn't been properly initialized");
  },

  isAddedToWatchlist: () => {
    throw new Error("`isAddedToWatchlist` in DataContext hasn't been properly initialized");
  },
  addToWatchlist: () => {
    throw new Error("`addToWatchlist` in DataContext hasn't been properly initialized");
  },
  removeFromWatchlist: () => {
    throw new Error("`removeFromWatchlist` in DataContext hasn't been properly initialized");
  },

  currentMovieListType: MovieListType.ALL,
  changeMovieListType: () => {
    throw new Error("`changeMovieListType` in DataContext hasn't been properly initialized");
  },

  openMovieModal: () => {
    throw new Error("`openMovieModal` in DataContext hasn't been properly initialized");
  },
  closeMovieModal: () => {
    throw new Error("`closeMovieModal` in DataContext hasn't been properly initialized");
  },
};

export const DataContext = createContext<DataContextType>(defaultContext);

export default function DataProvider({ children }: ExpectsChildren) {
  const { selectedSection, sectionOptions, changeSection } = useSectionSelector({
    movieSections: defaultContext.sections,
  });

  const { watchlist, isAddedToWatchlist, addToWatchlist, removeFromWatchlist } = useWatchlist();

  const { isOpen, open, close, movieToDisplay } = useModal();

  const [movieListType, changeMovieListType] = useState(defaultContext.currentMovieListType);

  const contextValue = useMemo((): DataContextType => {
    const currentEdition = getCurrentEdition();

    const screeningsByDay = ((): ScreeningsByDay => {
      const festivalDates = Editions.days(currentEdition);

      return festivalDates.reduce<ScreeningsByDay>(
        (accum, festivalDate) => ({
          ...accum,
          [serializeDate(festivalDate)]: getScreeningsForDay(window.MOVIES, festivalDate, {
            section: selectedSection?.value,
          }).filter((screening) =>
            movieListType === MovieListType.WATCHLIST ? isAddedToWatchlist(screening) : true,
          ),
        }),
        {},
      );
    })();

    const alwaysAvailableScreenings = getAlwaysAvailableScreenings(window.MOVIES, {
      section: selectedSection?.value,
    }).filter((screening) =>
      movieListType === MovieListType.WATCHLIST ? isAddedToWatchlist(screening) : true,
    );

    return {
      ...defaultContext,
      currentEdition,

      screeningsByDay,
      alwaysAvailableScreenings,
      selectedSection,
      sectionOptions,
      changeSection,

      isAddedToWatchlist,
      addToWatchlist,
      removeFromWatchlist,

      currentMovieListType: movieListType,
      changeMovieListType,

      openMovieModal: open,
      closeMovieModal: close,
    };
  }, [
    selectedSection,
    sectionOptions,
    changeSection,
    isAddedToWatchlist,
    addToWatchlist,
    removeFromWatchlist,
    movieListType,
    changeMovieListType,
    watchlist,
    open,
    close,
  ]);

  return (
    <DataContext.Provider value={contextValue}>
      {children}

      <MovieModal isOpen={isOpen} onRequestClose={close}>
        {movieToDisplay && <MovieModalContent movie={movieToDisplay} onClose={close} />}
      </MovieModal>
    </DataContext.Provider>
  );
}
