import { useTimeZone, VIEWER_TIME_ZONES } from '@shared/ts/__fixtures__/timeZone';

import { getDayAbbrev } from './dayUtils';

describe('getDayAbbrev', () => {
  it.each([
    // Using known dates — 2024-01-01 was a Monday
    ['2024-01-01', 'LUN'],
    ['2024-01-02', 'MAR'],
    ['2024-01-03', 'MIÉ'],
    ['2024-01-04', 'JUE'],
    ['2024-01-05', 'VIE'],
    ['2024-01-06', 'SÁB'],
    ['2024-01-07', 'DOM'],
  ])('returns Spanish abbreviation for %s → %s', (dateStr, expected) => {
    expect(getDayAbbrev(new Date(dateStr + 'T12:00:00'))).toBe(expected);
  });

  describe.each(VIEWER_TIME_ZONES)('viewer in %s', (timeZone) => {
    useTimeZone(timeZone);

    it('returns the Buenos Aires weekday of a festival day', () => {
      // Days start at midnight in Buenos Aires: still Wednesday 18 in New York
      expect(getDayAbbrev(new Date('2026-11-19T00:00:00-03:00'))).toBe('JUE');
    });

    it('returns the Buenos Aires weekday of a late screening', () => {
      // Tuesday 24 at 22:40 in Buenos Aires: already Wednesday 25 in London
      expect(getDayAbbrev(new Date('2026-11-24T22:40:00-03:00'))).toBe('MAR');
    });
  });
});
