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

export function dateHasPassed(date: Date): boolean {
  const now = new Date();
  now.setHours(0, 0, 0, 0);

  const dateWithoutTime = new Date(date);
  dateWithoutTime.setHours(0, 0, 0, 0);

  return dateWithoutTime < now;
}

const fmtBuenosAires = (d: Date) =>
  d.toLocaleDateString('en-CA', { timeZone: 'America/Argentina/Buenos_Aires' });

export function isTodayInBuenosAires(date: Date): boolean {
  return fmtBuenosAires(date) === fmtBuenosAires(new Date());
}

export function isTodayInBuenosAiresBetween(from: Date, to: Date): boolean {
  const today = fmtBuenosAires(new Date());
  return fmtBuenosAires(from) <= today && today <= fmtBuenosAires(to);
}

export function isDateBetween(date: Date, from: Date, to: Date): boolean {
  const fromWithoutTime = new Date(from);
  fromWithoutTime.setHours(0, 0, 0, 0);

  const oneDayAfterTo = new Date(to);
  oneDayAfterTo.setDate(to.getDate() + 1);
  oneDayAfterTo.setHours(0, 0, 0, 0);

  return fromWithoutTime <= date && date <= oneDayAfterTo;
}
