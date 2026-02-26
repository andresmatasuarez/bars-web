import { ReactNode, useContext } from 'react';
import styled from 'styled-components';

import { Stylable } from '../../types';
import { explodeCommaSeparated } from '../../utils';
import { DataContext } from '../DataProvider';
import { InlineHeading, MovieTitle, SectionLabel, Specs } from './commons';
import { MovieAdditionalData, MovieBlockAdditionalData } from './types';

export function getMoviesSpecs(
  movieData: MovieAdditionalData | MovieBlockAdditionalData['movies'][number],
): ReactNode {
  let specs: ReactNode = movieData.year || '';

  const country = movieData.country;
  if (country) {
    specs = specs ? `${specs} - ` : specs;
    specs = `${specs}${country}`;
  }

  const runtime = movieData.runtime;
  if (runtime) {
    specs = specs ? `${specs} - ` : specs;
    specs = (
      <>
        {specs} {runtime} min.
      </>
    );
  }

  return specs;
}

export default styled(function Info({
  className,
  movieData,
}: Stylable & {
  movieData: MovieAdditionalData | MovieBlockAdditionalData['movies'][number];
}) {
  const { sections } = useContext(DataContext);

  return (
    <div className={className}>
      {movieData.section && sections[movieData.section] && (
        <SectionLabel>{sections[movieData.section]}</SectionLabel>
      )}

      <MovieTitle>{movieData.title}</MovieTitle>

      <Specs>{getMoviesSpecs(movieData)}</Specs>

      {movieData.directors && (
        <Specs cssStyle="margin-bottom: 5px;">
          <InlineHeading>Dirigida por</InlineHeading>
          <div>{explodeCommaSeparated(movieData.directors).join(', ')}</div>
        </Specs>
      )}

      {movieData.cast && (
        <Specs>
          <InlineHeading>Elenco</InlineHeading>
          <div>{explodeCommaSeparated(movieData.cast).join(', ')}</div>
        </Specs>
      )}
    </div>
  );
})`
  > ${SectionLabel} {
    margin-bottom: 10px;
  }

  > ${Specs} {
    margin-bottom: 10px;
  }
`;
