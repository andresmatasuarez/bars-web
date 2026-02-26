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
});
