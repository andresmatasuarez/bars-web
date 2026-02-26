import type { JuryMember } from './JuryModal';
import { buildJuryDocumentTitle } from './JuryModal';

function makeMember(overrides: Partial<JuryMember> = {}): JuryMember {
  return {
    id: 1,
    name: 'María López',
    section: 'Terror',
    photoUrl: '/img/maria.jpg',
    slug: 'maria-lopez',
    bio: '<p>Bio text</p>',
    ...overrides,
  };
}

describe('buildJuryDocumentTitle', () => {
  beforeEach(() => {
    vi.stubGlobal('CURRENT_EDITION', 26);
    vi.stubGlobal('BASE_PAGE_TITLE', 'Premios y Jurados – Buenos Aires Rojo Sangre');
  });

  afterEach(() => {
    vi.unstubAllGlobals();
  });

  it('edition + section → "Name (BARS XXVI - Jurado Terror) – Site"', () => {
    expect(buildJuryDocumentTitle(makeMember())).toBe(
      'María López (BARS XXVI - Jurado Terror) – Buenos Aires Rojo Sangre',
    );
  });

  it('edition + no section → "Name (BARS XXVI) – Site"', () => {
    expect(buildJuryDocumentTitle(makeMember({ section: '' }))).toBe(
      'María López (BARS XXVI) – Buenos Aires Rojo Sangre',
    );
  });

  it('no edition + section → "Name (Jurado Terror) – Site"', () => {
    vi.stubGlobal('CURRENT_EDITION', undefined);

    expect(buildJuryDocumentTitle(makeMember())).toBe(
      'María López (Jurado Terror) – Buenos Aires Rojo Sangre',
    );
  });

  it('no edition + no section → "Name – Site"', () => {
    vi.stubGlobal('CURRENT_EDITION', undefined);

    expect(buildJuryDocumentTitle(makeMember({ section: '' }))).toBe(
      'María López – Buenos Aires Rojo Sangre',
    );
  });
});
