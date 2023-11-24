import styled from 'styled-components';

import { DangerousHTML, Divider } from '../../App/commons';
import { Movie, Stylable } from '../../types';
import { MovieHeader, MovieImageWrapper } from './commons';
import Info from './Info';
import MoviesCarousel from './MoviesCarousel';
import ScreeningsByVenue from './ScreeningsByVenue';
import { MovieBlockAdditionalData } from './types';

export default styled(function MovieBlockModalContent({
  className,
  movie,
  movieData,
}: Stylable & { movie: Movie; movieData: MovieBlockAdditionalData }) {
  return (
    <div className={className}>
      <MovieHeader>
        <MovieImageWrapper>
          <DangerousHTML html={movieData.image} />
        </MovieImageWrapper>

        <Info movieData={movieData} />
      </MovieHeader>

      <Divider />

      <MoviesCarousel movies={movieData.movies} />

      <Divider />

      <ScreeningsByVenue movie={movie} movieData={movieData} />
    </div>
  );
})`
  > ${Divider} {
    margin-top: 20px;
    margin-bottom: 20px;
  }
`;
