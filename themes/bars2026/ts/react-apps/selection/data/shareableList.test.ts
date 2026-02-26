import type { WatchlistEntry } from '@shared/ts/useWatchlist';

import {
  buildShareUrl,
  decodeShareableList,
  encodeShareableList,
  MAX_LIST_NAME,
  sanitizeListName,
  validateEntry,
} from './shareableList';

describe('sanitizeListName', () => {
  it('strips HTML tags', () => {
    expect(sanitizeListName('<b>Mi lista</b>')).toBe('Mi lista');
  });

  it('preserves accented characters', () => {
    expect(sanitizeListName('Películas favoritas')).toBe('Películas favoritas');
  });

  it('strips emojis and disallowed characters', () => {
    const result = sanitizeListName('Lista 🎬 #1!');
    // Emoji and # stripped, "Lista  1!" remains
    expect(result).not.toContain('🎬');
    expect(result).not.toContain('#');
    expect(result).toContain('Lista');
    expect(result).toContain('1!');
  });

  it('truncates to 30 characters', () => {
    const long = 'A'.repeat(50);
    expect(sanitizeListName(long)).toHaveLength(MAX_LIST_NAME);
  });

  it('returns empty string for all whitespace', () => {
    expect(sanitizeListName('   ')).toBe('');
  });

  it('returns empty string for empty string', () => {
    expect(sanitizeListName('')).toBe('');
  });
});

describe('validateEntry', () => {
  it('accepts a valid entry', () => {
    expect(validateEntry('123_[belgrano.Sala 6:11-20-2025 20:00]')).toBe(true);
  });

  it('rejects missing brackets', () => {
    expect(validateEntry('123_belgrano')).toBe(false);
  });

  it('rejects non-numeric ID', () => {
    expect(validateEntry('abc_[foo]')).toBe(false);
  });

  it('rejects empty string', () => {
    expect(validateEntry('')).toBe(false);
  });

  it('rejects number input', () => {
    expect(validateEntry(42)).toBe(false);
  });

  it('rejects null', () => {
    expect(validateEntry(null)).toBe(false);
  });

  it('rejects undefined', () => {
    expect(validateEntry(undefined)).toBe(false);
  });
});

describe('encodeShareableList / decodeShareableList', () => {
  const validEntries: WatchlistEntry[] = [
    '100_[belgrano.Sala 6:11-20-2025 20:00]',
    '200_[streaming!flixxo:11-21-2025]',
  ];

  it('roundtrips: encode then decode returns same name + entries', () => {
    const encoded = encodeShareableList('Mi lista', validEntries);
    const decoded = decodeShareableList(encoded);
    expect(decoded).not.toBeNull();
    expect(decoded!.name).toBe('Mi lista');
    expect(decoded!.entries).toEqual(validEntries);
  });

  it('roundtrips with accented name (UTF-8 encoding)', () => {
    const name = 'Películas increíbles';
    const encoded = encodeShareableList(name, validEntries);
    const decoded = decodeShareableList(encoded);
    expect(decoded).not.toBeNull();
    expect(decoded!.name).toBe(name);
  });

  it('caps entries at 200', () => {
    const manyEntries = Array.from(
      { length: 201 },
      (_, i): WatchlistEntry => `${i}_[belgrano.Sala 6:11-20-2025 20:00]`,
    );
    const encoded = encodeShareableList('Lista', manyEntries);
    const decoded = decodeShareableList(encoded);
    expect(decoded!.entries).toHaveLength(200);
  });

  it('sanitizes name during encode AND decode', () => {
    const encoded = encodeShareableList('<script>alert(1)</script>Lista', validEntries);
    const decoded = decodeShareableList(encoded);
    expect(decoded!.name).not.toContain('<script>');
    expect(decoded!.name).toContain('Lista');
  });

  it('returns null for malformed base64', () => {
    expect(decodeShareableList('!!!not-base64!!!')).toBeNull();
  });

  it('returns null for valid base64 but not valid JSON', () => {
    const encoded = btoa('not json at all');
    expect(decodeShareableList(encoded)).toBeNull();
  });

  it('returns null for valid JSON missing n field', () => {
    const encoded = btoa(JSON.stringify({ e: validEntries }));
    expect(decodeShareableList(encoded)).toBeNull();
  });

  it('returns null for valid JSON missing e field', () => {
    const encoded = btoa(JSON.stringify({ n: 'Lista' }));
    expect(decodeShareableList(encoded)).toBeNull();
  });

  it('returns null when n is not a string', () => {
    const encoded = btoa(JSON.stringify({ n: 42, e: validEntries }));
    expect(decodeShareableList(encoded)).toBeNull();
  });

  it('returns null when e is not an array', () => {
    const encoded = btoa(JSON.stringify({ n: 'Lista', e: 'not-array' }));
    expect(decodeShareableList(encoded)).toBeNull();
  });

  it('returns null when name is empty after sanitize', () => {
    const encoded = btoa(JSON.stringify({ n: '   ', e: validEntries }));
    expect(decodeShareableList(encoded)).toBeNull();
  });

  it('returns null when all entries are invalid format', () => {
    const encoded = btoa(JSON.stringify({ n: 'Lista', e: ['bad1', 'bad2'] }));
    expect(decodeShareableList(encoded)).toBeNull();
  });

  it('keeps only valid entries when mixed valid/invalid', () => {
    const mixed = ['bad-entry', validEntries[0], 42, validEntries[1]];
    const encoded = btoa(JSON.stringify({ n: 'Lista', e: mixed }));
    const decoded = decodeShareableList(encoded);
    expect(decoded).not.toBeNull();
    expect(decoded!.entries).toEqual(validEntries);
  });

  it('returns null for oversized payload (>50KB)', () => {
    const huge = 'A'.repeat(51_000);
    expect(decodeShareableList(huge)).toBeNull();
  });
});

describe('buildShareUrl', () => {
  const entries: WatchlistEntry[] = ['100_[belgrano.Sala 6:11-20-2025 20:00]'];

  beforeEach(() => {
    vi.stubGlobal('location', {
      origin: 'https://rojosangre.com.ar',
      pathname: '/2.0/seleccion/',
      search: '?e=26&f=some-film',
    });
  });

  afterEach(() => {
    vi.unstubAllGlobals();
  });

  it('sets list param with encoded value', () => {
    const url = buildShareUrl('Mi lista', entries);
    expect(url).toContain('list=');
    expect(url).toContain('https://rojosangre.com.ar/2.0/seleccion/');
  });

  it('strips f param', () => {
    const url = buildShareUrl('Mi lista', entries);
    expect(url).not.toContain('f=some-film');
  });

  it('preserves other existing params', () => {
    const url = buildShareUrl('Mi lista', entries);
    expect(url).toContain('e=26');
  });
});
