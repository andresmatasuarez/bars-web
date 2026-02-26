import {
  createAlwaysAvailableStreaming,
  createRegularStreaming,
  createTraditionalScreening,
} from '@shared/ts/__fixtures__/movies';
import Editions from '@shared/ts/Editions';
import type { Screening } from '@shared/ts/types';

import { getSpanishDayAbbr, resolveShortVerState } from './helpers';

describe('getSpanishDayAbbr', () => {
  it('returns JUE for a Thursday', () => {
    // 2024-01-04 is a Thursday
    expect(getSpanishDayAbbr(new Date('2024-01-04T12:00:00'))).toBe('JUE');
  });

  it('returns DOM for a Sunday (period stripped)', () => {
    // 2024-01-07 is a Sunday
    expect(getSpanishDayAbbr(new Date('2024-01-07T12:00:00'))).toBe('DOM');
  });
});

describe('resolveShortVerState', () => {
  beforeEach(() => {
    vi.useFakeTimers();
  });

  afterEach(() => {
    vi.useRealTimers();
  });

  function makeEdition(from: string | null, to: string | null) {
    const base = Editions.getByNumber(26);
    return {
      ...base,
      days: {
        ...base.days,
        from: from ?? undefined,
        to: to ?? undefined,
      },
    };
  }

  it('always-available + today in range → enabled', () => {
    vi.setSystemTime(new Date('2025-11-22T15:00:00-03:00'));
    const edition = makeEdition('2025-11-20T03:00:00.000Z', '2025-11-25T03:00:00.000Z');
    const screenings: Screening[] = [createAlwaysAvailableStreaming()];

    expect(resolveShortVerState(screenings, edition)).toEqual({ enabled: true });
  });

  it('always-available + festival over → disabled with "Ya no disponible"', () => {
    vi.setSystemTime(new Date('2025-12-01T15:00:00-03:00'));
    const edition = makeEdition('2025-11-20T03:00:00.000Z', '2025-11-25T03:00:00.000Z');
    const screenings: Screening[] = [createAlwaysAvailableStreaming()];

    expect(resolveShortVerState(screenings, edition)).toEqual({
      enabled: false,
      disabledCaption: 'Ya no disponible',
    });
  });

  it('always-available + festival not started → disabled with "Disponible durante el festival"', () => {
    vi.setSystemTime(new Date('2025-11-10T15:00:00-03:00'));
    const edition = makeEdition('2025-11-20T03:00:00.000Z', '2025-11-25T03:00:00.000Z');
    const screenings: Screening[] = [createAlwaysAvailableStreaming()];

    expect(resolveShortVerState(screenings, edition)).toEqual({
      enabled: false,
      disabledCaption: 'Disponible durante el festival',
    });
  });

  it('only traditional screenings + null dates → enabled (fallback)', () => {
    const edition = makeEdition(null, null);
    const screenings: Screening[] = [createTraditionalScreening()];

    expect(resolveShortVerState(screenings, edition)).toEqual({ enabled: true });
  });

  it('day-specific streaming + today matches → enabled', () => {
    vi.setSystemTime(new Date('2025-11-20T15:00:00-03:00'));
    const edition = makeEdition('2025-11-18T03:00:00.000Z', '2025-11-25T03:00:00.000Z');
    const screenings: Screening[] = [
      createRegularStreaming({ isoDate: '2025-11-20T03:00:00-03:00' }),
    ];

    expect(resolveShortVerState(screenings, edition)).toEqual({ enabled: true });
  });

  it('day-specific streaming + all dates passed → disabled with "Ya no disponible"', () => {
    vi.setSystemTime(new Date('2025-12-01T15:00:00-03:00'));
    const edition = makeEdition('2025-11-18T03:00:00.000Z', '2025-11-25T03:00:00.000Z');
    const screenings: Screening[] = [
      createRegularStreaming({ isoDate: '2025-11-20T03:00:00-03:00' }),
    ];

    expect(resolveShortVerState(screenings, edition)).toEqual({
      enabled: false,
      disabledCaption: 'Ya no disponible',
    });
  });

  it('day-specific streaming + dates in future → disabled with "Disponible el día de su proyección"', () => {
    vi.setSystemTime(new Date('2025-11-18T15:00:00-03:00'));
    const edition = makeEdition('2025-11-18T03:00:00.000Z', '2025-11-25T03:00:00.000Z');
    const screenings: Screening[] = [
      createRegularStreaming({ isoDate: '2025-11-22T03:00:00-03:00' }),
    ];

    expect(resolveShortVerState(screenings, edition)).toEqual({
      enabled: false,
      disabledCaption: 'Disponible el día de su proyección',
    });
  });
});
