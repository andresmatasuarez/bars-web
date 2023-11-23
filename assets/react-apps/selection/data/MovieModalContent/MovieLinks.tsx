import styled from 'styled-components';
import { faImdb } from '@fortawesome/free-brands-svg-icons';
import { faGlobe } from '@fortawesome/free-solid-svg-icons';
import { Stylable } from '../../types';
import { MovieAdditionalData, MovieBlockAdditionalData } from './types';
import { FAIcon } from '../../utils';

export default styled(function MovieLinks({
  className,
  movieData,
}: Stylable & {
  movieData: MovieAdditionalData | MovieBlockAdditionalData['movies'][number];
}) {
  return (
    <div className={className}>
      {movieData.imdb && (
        <a href={movieData.imdb} target="_blank" rel="noopener noreferrer">
          <FAIcon icon={faImdb} size="2x" title="Sitio IMDB" />
        </a>
      )}

      {movieData.website && (
        <a href={movieData.website} target="_blank" rel="noopener noreferrer">
          <FAIcon icon={faGlobe} size="2x" title="Sitio oficial" />
        </a>
      )}
    </div>
  );
})`
  display: flex;
  gap: 10px;

  > a {
    color: gray;

    &:hover {
      color: inherit;
    }
  }
`;
