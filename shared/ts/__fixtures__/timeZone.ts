/**
 * Where visitors may be. Buenos Aires itself; London, Madrid and Tokyo are ahead of it (a late
 * screening there is already the next day); New York is behind it (midnight in Buenos Aires
 * is still the day before there).
 */
export const VIEWER_TIME_ZONES = [
  'America/Argentina/Buenos_Aires',
  'Europe/London',
  'Europe/Madrid',
  'Asia/Tokyo',
  'America/New_York',
];

/**
 * Runs each test of the current `describe` block as if the viewer were in `timeZone`
 * (an IANA name such as 'Europe/London'). Node reads `process.env.TZ` again whenever it
 * changes, so both `Date` methods and `Intl` follow it.
 */
export function useTimeZone(timeZone: string) {
  let previous: string | undefined;

  beforeEach(() => {
    previous = process.env.TZ;
    process.env.TZ = timeZone;
  });

  afterEach(() => {
    if (previous === undefined) {
      delete process.env.TZ;
    } else {
      process.env.TZ = previous;
    }
  });
}
