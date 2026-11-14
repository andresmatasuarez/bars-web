export function parseDate(date?: string | number): Date | null {
  if (!date) {
    return null;
  }

  return new Date(date);
}

export function serializeDate(date: Date) {
  return date.toISOString();
}

export function getDayName(date: Date) {
  return date.toLocaleDateString('es-ar', { weekday: 'long' });
}

export function getDayNumber(date: Date): string {
  function pad(n: number, width: number, z = '0'): string {
    const str = String(n);
    return str.length >= width ? str : new Array(width - str.length + 1).join(z) + n;
  }

  return pad(date.getDate(), 2);
}

/** The festival's time zone. Screening dates and times are Buenos Aires dates and times. */
export const BUENOS_AIRES_TIME_ZONE = 'America/Argentina/Buenos_Aires';

/** The Buenos Aires day of `date` as "YYYY-MM-DD", wherever the viewer is. */
export function getBuenosAiresDateKey(date: Date): string {
  return date.toLocaleDateString('en-CA', { timeZone: BUENOS_AIRES_TIME_ZONE });
}

/** Whether the day of `date` is before today, both in Buenos Aires. */
export function dateHasPassed(date: Date): boolean {
  return getBuenosAiresDateKey(date) < getBuenosAiresDateKey(new Date());
}

/**
 * The day of the month of `date` in Buenos Aires. `date.getDate()` gives the day in the
 * viewer's time zone instead: a screening at 22:40 in Buenos Aires is already the next day
 * in Europe.
 */
export function getBuenosAiresDayOfMonth(date: Date): number {
  return Number(
    date.toLocaleDateString('en-US', { day: 'numeric', timeZone: BUENOS_AIRES_TIME_ZONE }),
  );
}

export function isTodayInBuenosAires(date: Date): boolean {
  return getBuenosAiresDateKey(date) === getBuenosAiresDateKey(new Date());
}

export function isTodayInBuenosAiresBetween(from: Date, to: Date): boolean {
  const today = getBuenosAiresDateKey(new Date());
  return getBuenosAiresDateKey(from) <= today && today <= getBuenosAiresDateKey(to);
}

export function isDateBetween(date: Date, from: Date, to: Date): boolean {
  const fromWithoutTime = new Date(from);
  fromWithoutTime.setHours(0, 0, 0, 0);

  const oneDayAfterTo = new Date(to);
  oneDayAfterTo.setDate(to.getDate() + 1);
  oneDayAfterTo.setHours(0, 0, 0, 0);

  return fromWithoutTime <= date && date <= oneDayAfterTo;
}
