import EDITIONS from '@shared/editions.json';

import Editions from './Editions';

describe('Editions', () => {
  describe('latest()', () => {
    it('returns the edition with the highest number', () => {
      const latest = Editions.latest();
      const maxNumber = Math.max(...EDITIONS.map((e) => e.number));
      expect(latest.number).toBe(maxNumber);
    });
  });

  describe('getByNumber()', () => {
    it('returns the correct edition for a known historical edition', () => {
      const edition = Editions.getByNumber(14);
      expect(edition.number).toBe(14);
      expect(edition.year).toBe(2013);
    });

    it('throws for a non-existent edition number', () => {
      expect(() => Editions.getByNumber(999)).toThrow('No edition found for number 999');
    });
  });

  describe('from() / to()', () => {
    it('returns a Date for a historical edition with dates', () => {
      const edition = Editions.getByNumber(14);
      const from = Editions.from(edition);
      const to = Editions.to(edition);
      expect(from).toBeInstanceOf(Date);
      expect(to).toBeInstanceOf(Date);
      expect(from!.getTime()).toBeLessThan(to!.getTime());
    });

    it('returns null when dates are null', () => {
      // Create a fake edition with null dates to test the null path
      const fakeEdition = { ...Editions.getByNumber(14), days: { from: null, to: null } };
      expect(Editions.from(fakeEdition as any)).toBeNull();
      expect(Editions.to(fakeEdition as any)).toBeNull();
    });
  });

  describe('days()', () => {
    it('returns correct number of days for a historical edition', () => {
      // Edition 14: 2013-10-31 to 2013-11-06 = 7 days
      const edition = Editions.getByNumber(14);
      const days = Editions.days(edition);
      const from = Editions.from(edition)!;
      const to = Editions.to(edition)!;
      const expectedCount =
        Math.round((to.getTime() - from.getTime()) / (1000 * 60 * 60 * 24)) + 1;
      expect(days).toHaveLength(expectedCount);
    });

    it('returns days in chronological order', () => {
      const edition = Editions.getByNumber(14);
      const days = Editions.days(edition);
      for (let i = 1; i < days.length; i++) {
        expect(days[i].getTime()).toBeGreaterThan(days[i - 1].getTime());
      }
    });

    it('throws when from date is null', () => {
      const fakeEdition = { ...Editions.getByNumber(14), number: 99, days: { from: null, to: '2025-11-30T03:00:00.000Z' } };
      expect(() => Editions.days(fakeEdition as any)).toThrow(
        'The start date of edition 99 must not be null',
      );
    });

    it('throws when to date is null', () => {
      const fakeEdition = { ...Editions.getByNumber(14), number: 99, days: { from: '2025-11-20T03:00:00.000Z', to: null } };
      expect(() => Editions.days(fakeEdition as any)).toThrow(
        'The end date of edition 99 must not be null',
      );
    });
  });

  describe('venues()', () => {
    it('returns venues for a historical edition', () => {
      const edition = Editions.getByNumber(14);
      const venues = Editions.venues(edition);
      expect(venues).toBeDefined();
      expect(Object.keys(venues)).toContain('lavalle');
    });
  });

  describe('getVenueName()', () => {
    it('returns venue name for a known venue', () => {
      const edition = Editions.getByNumber(14);
      expect(Editions.getVenueName('lavalle', edition)).toBe('Multiplex Lavalle');
    });

    it('throws for an unknown venue ID', () => {
      const edition = Editions.getByNumber(14);
      expect(() => Editions.getVenueName('nonexistent', edition)).toThrow(
        'Venue not found for ID "nonexistent"',
      );
    });
  });

  describe('romanNumerals()', () => {
    it('converts edition number to roman numerals', () => {
      const edition = Editions.getByNumber(26);
      expect(Editions.romanNumerals(edition)).toBe('XXVI');
    });
  });

  describe('getTitle()', () => {
    it('returns BARS + roman numeral', () => {
      const edition = Editions.getByNumber(26);
      expect(Editions.getTitle(edition)).toBe('BARS XXVI');
    });
  });
});
