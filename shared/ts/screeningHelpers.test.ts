import Editions from './Editions';
import {
  createAlwaysAvailableStreaming,
  createMovie,
  createRegularStreaming,
  createScreeningWithMovie,
  createTraditionalScreening,
} from './__fixtures__/movies';
import {
  compareScreenings,
  getAlwaysAvailableScreenings,
  getCurrentEdition,
  getScreeningsForDay,
  groupByDayAndTimeSlot,
  groupByTimeSlot,
  isLatestEdition,
} from './screeningHelpers';

describe('getCurrentEdition', () => {
  afterEach(() => {
    vi.unstubAllGlobals();
  });

  it('returns latest edition when no ?e= param', () => {
    vi.stubGlobal('location', { search: '' });
    const result = getCurrentEdition();
    expect(result.number).toBe(Editions.latest().number);
  });

  it('returns edition 26 when ?e=26', () => {
    vi.stubGlobal('location', { search: '?e=26' });
    expect(getCurrentEdition().number).toBe(26);
  });
});

describe('isLatestEdition', () => {
  afterEach(() => {
    vi.unstubAllGlobals();
  });

  it('returns true when no param', () => {
    vi.stubGlobal('location', { search: '' });
    expect(isLatestEdition()).toBe(true);
  });

  it('returns true when param matches latest edition number', () => {
    const latestNumber = Editions.latest().number;
    vi.stubGlobal('location', { search: `?e=${latestNumber}` });
    expect(isLatestEdition()).toBe(true);
  });

  it('returns false for a known older edition', () => {
    vi.stubGlobal('location', { search: '?e=14' });
    expect(isLatestEdition()).toBe(false);
  });
});

describe('getScreeningsForDay', () => {
  const nov20 = new Date('2025-11-20T03:00:00.000Z');
  const nov21 = new Date('2025-11-21T03:00:00.000Z');

  it('returns traditional screenings for that day', () => {
    const movies = [
      createMovie({
        screenings: [
          createTraditionalScreening({ isoDate: '2025-11-20T03:00:00-03:00', time: '20:00' }),
        ],
      }),
    ];
    const result = getScreeningsForDay(movies, nov20);
    expect(result).toHaveLength(1);
  });

  it('returns regular streaming screenings for that day', () => {
    const movies = [
      createMovie({
        screenings: [createRegularStreaming({ isoDate: '2025-11-20T03:00:00-03:00' })],
      }),
    ];
    const result = getScreeningsForDay(movies, nov20);
    expect(result).toHaveLength(1);
  });

  it('excludes always-available streaming', () => {
    const movies = [
      createMovie({
        screenings: [createAlwaysAvailableStreaming()],
      }),
    ];
    const result = getScreeningsForDay(movies, nov20);
    expect(result).toHaveLength(0);
  });

  it('excludes screenings from other days', () => {
    const movies = [
      createMovie({
        screenings: [
          createTraditionalScreening({ isoDate: '2025-11-21T03:00:00-03:00', time: '20:00' }),
        ],
      }),
    ];
    const result = getScreeningsForDay(movies, nov20);
    expect(result).toHaveLength(0);
  });

  it('filters by section when provided', () => {
    const movies = [
      createMovie({
        section: 'horror',
        screenings: [
          createTraditionalScreening({ isoDate: '2025-11-20T03:00:00-03:00', time: '20:00' }),
        ],
      }),
      createMovie({
        section: 'comedy',
        screenings: [
          createTraditionalScreening({ isoDate: '2025-11-20T03:00:00-03:00', time: '21:00' }),
        ],
      }),
    ];
    const result = getScreeningsForDay(movies, nov20, { section: 'horror' });
    expect(result).toHaveLength(1);
    expect(result[0].movie.section).toBe('horror');
  });

  it('returns all sections when no section filter', () => {
    const movies = [
      createMovie({
        section: 'horror',
        screenings: [
          createTraditionalScreening({ isoDate: '2025-11-20T03:00:00-03:00', time: '20:00' }),
        ],
      }),
      createMovie({
        section: 'comedy',
        screenings: [
          createTraditionalScreening({ isoDate: '2025-11-20T03:00:00-03:00', time: '21:00' }),
        ],
      }),
    ];
    const result = getScreeningsForDay(movies, nov20);
    expect(result).toHaveLength(2);
  });

  it('returns empty result for empty movies array', () => {
    expect(getScreeningsForDay([], nov20)).toEqual([]);
  });

  it('sorts results by compareScreenings order', () => {
    const movies = [
      createMovie({
        screenings: [
          createTraditionalScreening({
            isoDate: '2025-11-20T03:00:00-03:00',
            time: '22:00',
            raw: 'belgrano.Sala 6:11-20-2025 22:00',
          }),
        ],
      }),
      createMovie({
        screenings: [
          createRegularStreaming({ isoDate: '2025-11-20T03:00:00-03:00' }),
        ],
      }),
      createMovie({
        screenings: [
          createTraditionalScreening({
            isoDate: '2025-11-20T03:00:00-03:00',
            time: '18:00',
            raw: 'belgrano.Sala 6:11-20-2025 18:00',
          }),
        ],
      }),
    ];
    const result = getScreeningsForDay(movies, nov20);
    // Regular streaming first, then traditional sorted by time
    expect(result[0].streaming).toBe(true);
    expect((result[1] as any).time).toBe('18:00');
    expect((result[2] as any).time).toBe('22:00');
  });
});

describe('getAlwaysAvailableScreenings', () => {
  it('returns only always-available screenings', () => {
    const movies = [
      createMovie({
        screenings: [createAlwaysAvailableStreaming()],
      }),
      createMovie({
        screenings: [
          createTraditionalScreening({ isoDate: '2025-11-20T03:00:00-03:00', time: '20:00' }),
        ],
      }),
      createMovie({
        screenings: [createRegularStreaming({ isoDate: '2025-11-20T03:00:00-03:00' })],
      }),
    ];
    const result = getAlwaysAvailableScreenings(movies);
    expect(result).toHaveLength(1);
    expect(result[0].alwaysAvailable).toBe(true);
  });

  it('filters by section', () => {
    const movies = [
      createMovie({
        section: 'horror',
        screenings: [createAlwaysAvailableStreaming()],
      }),
      createMovie({
        section: 'comedy',
        screenings: [createAlwaysAvailableStreaming()],
      }),
    ];
    const result = getAlwaysAvailableScreenings(movies, { section: 'horror' });
    expect(result).toHaveLength(1);
    expect(result[0].movie.section).toBe('horror');
  });
});

describe('compareScreenings', () => {
  it('regular streaming always comes first', () => {
    const streaming = createRegularStreaming({ isoDate: '2025-11-20T03:00:00-03:00' });
    const traditional = createTraditionalScreening({
      isoDate: '2025-11-20T03:00:00-03:00',
      time: '18:00',
    });
    expect(compareScreenings(streaming, traditional)).toBe(-1);
  });

  it('traditional comes after regular streaming', () => {
    const traditional = createTraditionalScreening({
      isoDate: '2025-11-20T03:00:00-03:00',
      time: '18:00',
    });
    const streaming = createRegularStreaming({ isoDate: '2025-11-20T03:00:00-03:00' });
    expect(compareScreenings(traditional, streaming)).toBe(1);
  });

  it('two traditional: earlier isoDate first', () => {
    const early = createTraditionalScreening({
      isoDate: '2025-11-20T03:00:00-03:00',
      time: '20:00',
    });
    const late = createTraditionalScreening({
      isoDate: '2025-11-21T03:00:00-03:00',
      time: '20:00',
    });
    expect(compareScreenings(early, late)).toBeLessThan(0);
  });

  it('same isoDate: earlier time first', () => {
    const early = createTraditionalScreening({
      isoDate: '2025-11-20T03:00:00-03:00',
      time: '18:00',
    });
    const late = createTraditionalScreening({
      isoDate: '2025-11-20T03:00:00-03:00',
      time: '22:00',
    });
    expect(compareScreenings(early, late)).toBeLessThan(0);
  });

  it('both regular streaming: returns -1 (first always wins)', () => {
    const s1 = createRegularStreaming({ isoDate: '2025-11-20T03:00:00-03:00' });
    const s2 = createRegularStreaming({ isoDate: '2025-11-21T03:00:00-03:00' });
    expect(compareScreenings(s1, s2)).toBe(-1);
  });
});

describe('groupByTimeSlot', () => {
  it('groups traditional screenings by time string', () => {
    const s1 = createScreeningWithMovie(
      createTraditionalScreening({ isoDate: '2025-11-20T03:00:00-03:00', time: '20:00' }),
    );
    const s2 = createScreeningWithMovie(
      createTraditionalScreening({ isoDate: '2025-11-20T03:00:00-03:00', time: '20:00' }),
    );
    const s3 = createScreeningWithMovie(
      createTraditionalScreening({ isoDate: '2025-11-20T03:00:00-03:00', time: '22:00' }),
    );
    const groups = groupByTimeSlot([s1, s2, s3]);
    expect(groups.get('20:00')).toHaveLength(2);
    expect(groups.get('22:00')).toHaveLength(1);
  });

  it('groups regular streaming as "Online"', () => {
    const s1 = createScreeningWithMovie(
      createRegularStreaming({ isoDate: '2025-11-20T03:00:00-03:00' }),
    );
    const groups = groupByTimeSlot([s1]);
    expect(groups.get('Online')).toHaveLength(1);
  });

  it('multiple screenings same time are in same group', () => {
    const s1 = createScreeningWithMovie(
      createTraditionalScreening({ isoDate: '2025-11-20T03:00:00-03:00', time: '20:00' }),
    );
    const s2 = createScreeningWithMovie(
      createTraditionalScreening({ isoDate: '2025-11-20T03:00:00-03:00', time: '20:00' }),
    );
    const groups = groupByTimeSlot([s1, s2]);
    expect(groups.size).toBe(1);
    expect(groups.get('20:00')).toHaveLength(2);
  });
});

describe('groupByDayAndTimeSlot', () => {
  it('sorts multiple days chronologically', () => {
    const s1 = createScreeningWithMovie(
      createTraditionalScreening({ isoDate: '2025-11-21T03:00:00-03:00', time: '20:00' }),
    );
    const s2 = createScreeningWithMovie(
      createTraditionalScreening({ isoDate: '2025-11-20T03:00:00-03:00', time: '20:00' }),
    );
    const result = groupByDayAndTimeSlot([s1, s2]);
    expect(result).toHaveLength(2);
    expect(result[0].date.getTime()).toBeLessThan(result[1].date.getTime());
  });

  it('groups single day into one DayGroup', () => {
    const s1 = createScreeningWithMovie(
      createTraditionalScreening({ isoDate: '2025-11-20T03:00:00-03:00', time: '20:00' }),
    );
    const s2 = createScreeningWithMovie(
      createTraditionalScreening({ isoDate: '2025-11-20T03:00:00-03:00', time: '22:00' }),
    );
    const result = groupByDayAndTimeSlot([s1, s2]);
    expect(result).toHaveLength(1);
    expect(result[0].timeSlots.size).toBe(2);
  });

  it('groups each day screenings by time slot', () => {
    const s1 = createScreeningWithMovie(
      createTraditionalScreening({ isoDate: '2025-11-20T03:00:00-03:00', time: '20:00' }),
    );
    const s2 = createScreeningWithMovie(
      createTraditionalScreening({ isoDate: '2025-11-20T03:00:00-03:00', time: '20:00' }),
    );
    const result = groupByDayAndTimeSlot([s1, s2]);
    expect(result[0].timeSlots.get('20:00')).toHaveLength(2);
  });
});
