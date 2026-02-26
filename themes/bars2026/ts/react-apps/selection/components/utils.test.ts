import {
  createAlwaysAvailableStreaming,
  createScreeningWithMovie,
  createTraditionalScreening,
} from '@shared/ts/__fixtures__/movies';
import Editions from '@shared/ts/Editions';
import type { MovieSections } from '@shared/ts/types';

import { getSectionLabel, getVenueDisplay } from './utils';

describe('getVenueDisplay', () => {
  it('returns venue name + room for traditional screening with room', () => {
    const edition = Editions.getByNumber(14); // has "lavalle" → "Multiplex Lavalle"
    const screening = createScreeningWithMovie(
      createTraditionalScreening({ venue: 'lavalle', room: 'Sala 6' }),
    );

    expect(getVenueDisplay(screening, edition)).toBe('Multiplex Lavalle - Sala 6');
  });

  it('returns venue name only for traditional screening without room', () => {
    const edition = Editions.getByNumber(14);
    const screening = createScreeningWithMovie(
      createTraditionalScreening({ venue: 'lavalle', room: undefined }),
    );

    expect(getVenueDisplay(screening, edition)).toBe('Multiplex Lavalle');
  });

  it('returns venue name for streaming screening', () => {
    const edition = Editions.getByNumber(26); // has "flixxo" → "Flixxo"
    const screening = createScreeningWithMovie(createAlwaysAvailableStreaming({ venue: 'flixxo' }));

    expect(getVenueDisplay(screening, edition)).toBe('Flixxo');
  });

  it('falls back to raw venue ID for unknown venue', () => {
    const edition = Editions.getByNumber(26);
    const screening = createScreeningWithMovie(
      createTraditionalScreening({ venue: 'unknownvenue', room: undefined }),
    );

    expect(getVenueDisplay(screening, edition)).toBe('unknownvenue');
  });
});

describe('getSectionLabel', () => {
  const sections: MovieSections = {
    internationalFeatureFilmCompetition: 'Competencia Internacional',
    shortFilmCompetition: 'Cortos en competencia',
  };

  it('returns label when section exists in map', () => {
    const screening = createScreeningWithMovie(createTraditionalScreening(), {
      section: 'internationalFeatureFilmCompetition',
    });

    expect(getSectionLabel(screening, sections)).toBe('Competencia Internacional');
  });

  it('falls back to raw section key when section not in map', () => {
    const screening = createScreeningWithMovie(createTraditionalScreening(), {
      section: 'unknownSection',
    });

    expect(getSectionLabel(screening, sections)).toBe('unknownSection');
  });
});
