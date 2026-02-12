import { ReactNode } from 'react';
import { css, RuleSet } from 'styled-components';

// Re-export all shared types
export type {
  AlwaysAvailableStreamingScreening,
  BaseStreamingScreening,
  Movie,
  Movies,
  MovieSections,
  RegularStreamingScreening,
  Screening,
  ScreeningsByDay,
  ScreeningWithMovie,
  StreamingScreening,
  TraditionalScreening,
  Venues,
} from '@shared/ts/selection/types';
export {
  isRegularStreamingScreening,
  isScreeningAlwaysAvailable,
  isStreamingScreening,
  isTraditionalScreening,
} from '@shared/ts/selection/types';

// bars2013-specific styled-components utilities
export type Stylable = {
  className?: string;
  cssStyle?:
    | string

    // Result of css`...`
    | RuleSet<Stylable>;
};

export type ExpectsChildren = {
  children: ReactNode;
};

export const applyCssStyleProp = css<Stylable>`
  ${(props) =>
    props.cssStyle ||
    // default to empty string to avoid rendering
    // the 'undefined' word as part of the resulting CSS
    ''}
`;
