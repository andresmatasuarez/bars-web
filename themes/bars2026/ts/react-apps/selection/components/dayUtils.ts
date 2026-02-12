const SPANISH_DAY_ABBREVS: Record<string, string> = {
  monday: 'LUN',
  tuesday: 'MAR',
  wednesday: 'MIÉ',
  thursday: 'JUE',
  friday: 'VIE',
  saturday: 'SÁB',
  sunday: 'DOM',
};

export function getDayAbbrev(date: Date): string {
  const englishDay = date.toLocaleDateString('en-US', { weekday: 'long' }).toLowerCase();
  return SPANISH_DAY_ABBREVS[englishDay] ?? englishDay.slice(0, 3).toUpperCase();
}
