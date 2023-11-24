import styled from 'styled-components';

import { Divider } from '../../../App/commons';
import { Stylable } from '../../../types';
import { modalScrollbar } from '../../MovieModal';
import { MovieBlockAdditionalData } from '../types';
import MovieEntry from './MovieEntry';

const MovieEntries = styled.div`
  display: flex;
  flex-flow: column;
  height: 300px;
  overflow: auto;

  background: rgba(255, 255, 255, 0.05);
  border-radius: 10px;
  padding: 20px;

  ${modalScrollbar}

  > ${Divider} {
    margin-top: 15px;
    margin-bottom: 15px;
    flex: 0 0 1px;
  }
`;

export default styled(function MoviesCarousel({
  className,
  movies,
}: Stylable & {
  movies: MovieBlockAdditionalData['movies'];
}) {
  return (
    <div className={className}>
      <MovieEntries>
        {movies.map((movie, index) => (
          <>
            {index === 0 ? null : <Divider />}

            <MovieEntry movie={movie} />
          </>
        ))}
      </MovieEntries>
    </div>
  );
})`
  display: flex;
  flex-flow: column;
  gap: 10px;
`;
