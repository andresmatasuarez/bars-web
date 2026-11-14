import { BUENOS_AIRES_TIME_ZONE } from '@shared/ts/helpers';

const SPANISH_DAY_ABBREVS: Record<string, string> = {
  monday: 'LUN',
  tuesday: 'MAR',
  wednesday: 'MIÉ',
  thursday: 'JUE',
  friday: 'VIE',
  saturday: 'SÁB',
  sunday: 'DOM',
};

/** The weekday of `date` in Buenos Aires, wherever the viewer is ("LUN", "MAR", ...). */
export function getDayAbbrev(date: Date): string {
  const englishDay = date
    .toLocaleDateString('en-US', { weekday: 'long', timeZone: BUENOS_AIRES_TIME_ZONE })
    .toLowerCase();
  return SPANISH_DAY_ABBREVS[englishDay] ?? englishDay.slice(0, 3).toUpperCase();
}
