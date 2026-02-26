import {
  dateHasPassed,
  getDayName,
  getDayNumber,
  isDateBetween,
  isTodayInBuenosAires,
  isTodayInBuenosAiresBetween,
  parseDate,
  serializeDate,
} from './helpers';

describe('parseDate', () => {
  it('parses a string date', () => {
    const result = parseDate('2025-11-20T03:00:00.000Z');
    expect(result).toBeInstanceOf(Date);
    expect(result!.toISOString()).toBe('2025-11-20T03:00:00.000Z');
  });

  it('parses a numeric timestamp', () => {
    const ts = new Date('2025-11-20T03:00:00.000Z').getTime();
    const result = parseDate(ts);
    expect(result).toBeInstanceOf(Date);
    expect(result!.getTime()).toBe(ts);
  });

  it('returns null for undefined', () => {
    expect(parseDate(undefined)).toBeNull();
  });

  it('returns null for empty string', () => {
    expect(parseDate('')).toBeNull();
  });
});

describe('serializeDate', () => {
  it('returns ISO string', () => {
    const date = new Date('2025-11-20T03:00:00.000Z');
    expect(serializeDate(date)).toBe('2025-11-20T03:00:00.000Z');
  });
});

describe('getDayName', () => {
  it('returns Spanish weekday name', () => {
    // 2025-11-20 is a Thursday
    const date = new Date('2025-11-20T12:00:00.000Z');
    expect(getDayName(date)).toBe('jueves');
  });
});

describe('getDayNumber', () => {
  it('zero-pads single digit day', () => {
    const date = new Date('2025-11-05T12:00:00.000Z');
    expect(getDayNumber(date)).toBe('05');
  });

  it('does not pad double digit day', () => {
    const date = new Date('2025-11-20T12:00:00.000Z');
    expect(getDayNumber(date)).toBe('20');
  });
});

describe('dateHasPassed', () => {
  beforeEach(() => {
    vi.useFakeTimers();
    vi.setSystemTime(new Date('2025-11-20T15:00:00.000Z'));
  });

  afterEach(() => {
    vi.useRealTimers();
  });

  it('returns true for yesterday', () => {
    expect(dateHasPassed(new Date('2025-11-19T15:00:00.000Z'))).toBe(true);
  });

  it('returns false for today (strips time)', () => {
    expect(dateHasPassed(new Date('2025-11-20T03:00:00.000Z'))).toBe(false);
  });

  it('returns false for tomorrow', () => {
    expect(dateHasPassed(new Date('2025-11-21T15:00:00.000Z'))).toBe(false);
  });
});

describe('isTodayInBuenosAires', () => {
  beforeEach(() => {
    vi.useFakeTimers();
  });

  afterEach(() => {
    vi.useRealTimers();
  });

  it('returns true for matching BA date', () => {
    // 2025-11-20 02:00 UTC = 2025-11-19 23:00 BA time
    // Set system time to 2025-11-20 12:00 UTC = 2025-11-20 09:00 BA
    vi.setSystemTime(new Date('2025-11-20T12:00:00.000Z'));
    const date = new Date('2025-11-20T15:00:00.000Z'); // still Nov 20 in BA
    expect(isTodayInBuenosAires(date)).toBe(true);
  });

  it('returns false for non-matching date', () => {
    vi.setSystemTime(new Date('2025-11-20T12:00:00.000Z'));
    const date = new Date('2025-11-19T12:00:00.000Z');
    expect(isTodayInBuenosAires(date)).toBe(false);
  });
});

describe('isTodayInBuenosAiresBetween', () => {
  beforeEach(() => {
    vi.useFakeTimers();
  });

  afterEach(() => {
    vi.useRealTimers();
  });

  it('returns true when today is in range', () => {
    vi.setSystemTime(new Date('2025-11-22T12:00:00.000Z'));
    const from = new Date('2025-11-20T03:00:00.000Z');
    const to = new Date('2025-11-25T03:00:00.000Z');
    expect(isTodayInBuenosAiresBetween(from, to)).toBe(true);
  });

  it('returns false when today is before range', () => {
    vi.setSystemTime(new Date('2025-11-19T12:00:00.000Z'));
    const from = new Date('2025-11-20T03:00:00.000Z');
    const to = new Date('2025-11-25T03:00:00.000Z');
    expect(isTodayInBuenosAiresBetween(from, to)).toBe(false);
  });

  it('returns false when today is after range', () => {
    vi.setSystemTime(new Date('2025-11-26T12:00:00.000Z'));
    const from = new Date('2025-11-20T03:00:00.000Z');
    const to = new Date('2025-11-25T03:00:00.000Z');
    expect(isTodayInBuenosAiresBetween(from, to)).toBe(false);
  });
});

describe('isDateBetween', () => {
  const from = new Date('2025-11-20T03:00:00.000Z');
  const to = new Date('2025-11-30T03:00:00.000Z');

  it('returns true for a date in range', () => {
    expect(isDateBetween(new Date('2025-11-25T12:00:00.000Z'), from, to)).toBe(true);
  });

  it('returns true exactly on from date', () => {
    expect(isDateBetween(new Date('2025-11-20T03:00:00.000Z'), from, to)).toBe(true);
  });

  it('returns true exactly on to date (includes +1 day)', () => {
    // to is Nov 30, the function adds one day so Dec 1 00:00 is the boundary
    expect(isDateBetween(new Date('2025-11-30T12:00:00.000Z'), from, to)).toBe(true);
  });

  it('returns false one day after to', () => {
    expect(isDateBetween(new Date('2025-12-02T00:00:00.000Z'), from, to)).toBe(false);
  });

  it('returns false before from', () => {
    expect(isDateBetween(new Date('2025-11-19T12:00:00.000Z'), from, to)).toBe(false);
  });
});
