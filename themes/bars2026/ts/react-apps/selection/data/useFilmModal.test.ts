import { createMovie } from '@shared/ts/__fixtures__/movies';

import { buildDocumentTitle } from './useFilmModal';

describe('buildDocumentTitle', () => {
  beforeEach(() => {
    vi.stubGlobal('CURRENT_EDITION', 26);
    vi.stubGlobal('MOVIE_SECTIONS', {
      internationalFeatureFilmCompetition: 'Competencia Internacional',
    });
    vi.stubGlobal('BASE_PAGE_TITLE', 'Programación – Buenos Aires Rojo Sangre');
  });

  afterEach(() => {
    vi.unstubAllGlobals();
  });

  it('includes section label when section is known', () => {
    const movie = createMovie({
      title: 'Película de terror',
      section: 'internationalFeatureFilmCompetition',
    });

    expect(buildDocumentTitle(movie)).toBe(
      'Película de terror (BARS XXVI - Competencia Internacional) – Buenos Aires Rojo Sangre',
    );
  });

  it('omits section label when section is unknown', () => {
    const movie = createMovie({
      title: 'Película indie',
      section: 'unknownSection',
    });

    expect(buildDocumentTitle(movie)).toBe(
      'Película indie (BARS XXVI) – Buenos Aires Rojo Sangre',
    );
  });

  it('handles empty BASE_PAGE_TITLE gracefully', () => {
    vi.stubGlobal('BASE_PAGE_TITLE', '');
    const movie = createMovie({
      title: 'Mi película',
      section: 'internationalFeatureFilmCompetition',
    });

    expect(buildDocumentTitle(movie)).toBe(
      'Mi película (BARS XXVI - Competencia Internacional) – ',
    );
  });
});
