export function getSpanishDayAbbr(date: Date): string {
  const day = date.toLocaleDateString('es-AR', { weekday: 'short' });
  return day.replace('.', '').toUpperCase().slice(0, 3);
}
