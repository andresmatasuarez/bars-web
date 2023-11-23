import styled from 'styled-components';
import { DangerousHTML } from '../../../App/commons';
import { Stylable } from '../../../types';
import Info from '../Info';
import MovieLinks from '../MovieLinks';
import { InlineHeading, MovieHeader, MovieImageWrapper, Paragraph } from '../commons';
import { MovieBlockAdditionalData } from '../types';

export default styled(function MovieEntry({
  className,
  movie,
}: Stylable & {
  movie: MovieBlockAdditionalData['movies'][number];
}) {
  return (
    <div className={className}>
      <MovieHeader>
        <MovieImageWrapper>
          <DangerousHTML html={movie.image} />

          <MovieLinks movieData={movie} />
        </MovieImageWrapper>
        <Info movieData={movie} />
      </MovieHeader>

      {movie.synopsis && (
        <Paragraph>
          <InlineHeading>Synopsis</InlineHeading>
          <DangerousHTML html={movie.synopsis} />
        </Paragraph>
      )}
    </div>
  );
})`
  padding: 0 15px;

  > ${MovieHeader} {
    flex-flow: row-reverse;
    justify-content: space-between;
  }
`;
