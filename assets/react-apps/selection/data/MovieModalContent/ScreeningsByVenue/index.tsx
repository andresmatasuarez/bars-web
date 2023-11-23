import styled from 'styled-components';
import { Movie, Screening, Stylable } from '../../../types';
import ScreeningsGroup from './ScreeningsGroup';
import { getCurrentEdition } from '../../helpers';
import Editions from '../../../Editions';
import { MovieAdditionalData } from '../types';

type ScreeningByVenue = Record</* Venue */ string, Screening[]>;

function groupScreeningsByVenue(screenings: Screening[]): ScreeningByVenue {
  return screenings.reduce<ScreeningByVenue>((accum, screening) => {
    if (accum[screening.venue]) {
      return {
        ...accum,
        [screening.venue]: [...accum[screening.venue], screening],
      };
    }

    return {
      ...accum,
      [screening.venue]: [screening],
    };
  }, {});
}

export default styled(function ScreeningsByVenue({
  className,
  movie,
  movieData,
}: Stylable & {
  movie: Movie;
  movieData: MovieAdditionalData;
}) {
  const groupedScreenings = groupScreeningsByVenue(movie.screenings);
  const currentEdition = getCurrentEdition();

  return (
    <div className={className}>
      {Object.entries(groupedScreenings).map(([venue, screenings]) => (
        <ScreeningsGroup
          title={Editions.getVenueName(venue, currentEdition)}
          movie={movie}
          movieData={movieData}
          screenings={screenings}
        />
      ))}
    </div>
  );
})`
  display: flex;
  justify-content: center;
  gap: 30px;
`;
