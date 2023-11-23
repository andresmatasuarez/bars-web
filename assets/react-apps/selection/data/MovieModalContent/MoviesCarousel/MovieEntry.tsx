import styled from 'styled-components';
import { Stylable } from '../../../types';
import { MovieBlockAdditionalData } from '../types';
import { InlineHeading, MovieHeader, MovieImageWrapper, Paragraph } from '../commons';
import { DangerousHTML } from '../../../App/commons';
import MovieLinks from '../MovieLinks';
import Info from '../Info';

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
