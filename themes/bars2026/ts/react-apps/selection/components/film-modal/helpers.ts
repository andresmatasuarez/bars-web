import Editions, { SingleEdition } from '@shared/ts/selection/Editions';
import { dateHasPassed, isTodayInBuenosAires, isTodayInBuenosAiresBetween } from '@shared/ts/selection/helpers';
import { isRegularStreamingScreening, isScreeningAlwaysAvailable, Screening } from '@shared/ts/selection/types';

export function getSpanishDayAbbr(date: Date): string {
  const day = date.toLocaleDateString('es-AR', { weekday: 'short' });
  return day.replace('.', '').toUpperCase().slice(0, 3);
}

/**
 * Resolves whether a short film's "Ver" button should be enabled,
 * based on the parent block's screenings.
 */
export function resolveShortVerState(
  screenings: Screening[],
  currentEdition: SingleEdition,
): { enabled: boolean; disabledCaption?: string } {
  const hasAlwaysAvailable = screenings.some(isScreeningAlwaysAvailable);
  const daySpecific = screenings.filter(isRegularStreamingScreening);

  // If block has any always-available streaming OR only traditional screenings → festival date range
  if (hasAlwaysAvailable || daySpecific.length === 0) {
    const from = Editions.from(currentEdition);
    const to = Editions.to(currentEdition);
    if (from && to) {
      const enabled = isTodayInBuenosAiresBetween(from, to);
      return {
        enabled,
        disabledCaption: enabled
          ? undefined
          : dateHasPassed(to) ? 'Ya no disponible' : 'Disponible durante el festival',
      };
    }
    return { enabled: true };
  }

  // Day-specific streaming → enabled if today matches any screening day
  const enabled = daySpecific.some(s => isTodayInBuenosAires(new Date(s.isoDate)));
  if (enabled) return { enabled: true };

  // All day-specific dates missed — pick latest to decide caption
  const latest = daySpecific.reduce((a, b) => new Date(a.isoDate) > new Date(b.isoDate) ? a : b);
  return {
    enabled: false,
    disabledCaption: dateHasPassed(new Date(latest.isoDate))
      ? 'Ya no disponible'
      : 'Disponible el día de su proyección',
  };
}
