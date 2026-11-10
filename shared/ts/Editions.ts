import EDITIONS from '@shared/editions.json';
import integerToRoman from '@shared/ts/integerToRoman';

import { parseDate } from './helpers';
import { Venues } from './types';

type EditionInJson = (typeof EDITIONS)[number];

/**
 * TODO
 * specify the actual type instead using `typeof`
 *
 * The festival dates (`days.from` / `days.to`) can be missing: an edition can be
 * announced before its dates are known, and `Editions.from()` / `Editions.to()`
 * return null then. The type inferred from editions.json cannot show this while
 * every edition in the file has dates, so it is stated here.
 */
export type SingleEdition = Omit<EditionInJson, 'days'> & {
  days: Omit<EditionInJson['days'], 'from' | 'to'> & { from?: string; to?: string };
};

export default class Editions {
  /**
   * TODO validate JSON form with zod or something
   */
  private static raw = EDITIONS;

  static latest(): SingleEdition {
    return this.raw.reduce((interimMax, edition) => {
      return interimMax.number >= edition.number ? interimMax : edition;
    });
  }

  static getByNumber(editionNumber: number): SingleEdition {
    const result = this.raw.find((edition) => edition.number === editionNumber);

    if (!result) {
      throw new Error(`No edition found for number ${editionNumber}`);
    }

    return result;
  }

  static from(edition: SingleEdition = this.latest()): Date | null {
    const fromValue = edition.days.from;
    return parseDate(fromValue);
  }

  static to(edition: SingleEdition = this.latest()): Date | null {
    const toValue = edition.days.to;
    return parseDate(toValue);
  }

  static days(edition: SingleEdition = this.latest()): Date[] {
    const fromDate = this.from(edition);
    const toDate = this.to(edition);

    if (!fromDate) {
      throw new Error(
        `Editions :: The start date of edition ${edition.number} must not be null at this point.`,
      );
    }

    if (!toDate) {
      throw new Error(
        `Editions :: The end date of edition ${edition.number} must not be null at this point.`,
      );
    }

    const daysBetween: Date[] = [];

    const currentDate = fromDate;
    while (currentDate <= toDate) {
      daysBetween.push(new Date(currentDate));
      currentDate.setDate(currentDate.getDate() + 1);
    }

    return daysBetween;
  }

  static venues(edition: SingleEdition = this.latest()): Venues {
    return edition.venues ?? null;
  }

  static getVenueName(venueId: string, edition: SingleEdition = this.latest()): string {
    const venues = this.venues(edition);

    const venue = venues[venueId];
    if (!venue) {
      throw new Error(`Venue not found for ID "${venueId}".`);
    }

    return venue.name;
  }

  static romanNumerals(edition: SingleEdition): string {
    return integerToRoman(edition.number);
  }

  static getTitle(edition: SingleEdition): string {
    return 'BARS ' + integerToRoman(edition.number);
  }
}
