import {
  createAlwaysAvailableStreaming,
  createMovie,
  createTraditionalScreening,
} from '@shared/ts/__fixtures__/movies';

import { getInitialTab, resolveEntries } from './DataProvider';

describe('getInitialTab', () => {
  beforeEach(() => {
    vi.useFakeTimers();
  });

  afterEach(() => {
    vi.useRealTimers();
  });

  it('returns { type: "online" } when days array is empty', () => {
    expect(getInitialTab([])).toEqual({ type: 'online' });
  });

  it('returns { type: "all" } when last day has passed', () => {
    vi.setSystemTime(new Date('2025-12-01T15:00:00'));
    const days = [
      new Date('2025-11-20T03:00:00'),
      new Date('2025-11-21T03:00:00'),
      new Date('2025-11-22T03:00:00'),
    ];

    expect(getInitialTab(days)).toEqual({ type: 'all' });
  });

  it('returns { type: "day" } with today when current date is during festival', () => {
    vi.setSystemTime(new Date('2025-11-21T15:00:00'));
    const days = [
      new Date('2025-11-20T03:00:00'),
      new Date('2025-11-21T03:00:00'),
      new Date('2025-11-22T03:00:00'),
    ];

    const result = getInitialTab(days);
    expect(result).toEqual({ type: 'day', date: days[1] });
  });

  it('returns { type: "day" } with first day when before festival', () => {
    vi.setSystemTime(new Date('2025-11-10T15:00:00'));
    const days = [
      new Date('2025-11-20T03:00:00'),
      new Date('2025-11-21T03:00:00'),
      new Date('2025-11-22T03:00:00'),
    ];

    const result = getInitialTab(days);
    expect(result).toEqual({ type: 'day', date: days[0] });
  });
});

describe('resolveEntries', () => {
  afterEach(() => {
    vi.unstubAllGlobals();
  });

  it('keeps entry matching a day screening', () => {
    const isoDate = '2025-11-20T03:00:00-03:00';
    const raw = 'belgrano.Sala 6:11-20-2025 20:00';
    const movie = createMovie({
      id: 42,
      screenings: [createTraditionalScreening({ isoDate, raw, time: '20:00' })],
    });
    vi.stubGlobal('MOVIES', [movie]);

    const day = new Date(isoDate);
    const entry = `${movie.id}_[${raw}]`;
    expect(resolveEntries([entry], [day])).toEqual([entry]);
  });

  it('keeps entry matching always-available screening', () => {
    const raw = 'streaming!flixxo:full';
    const movie = createMovie({
      id: 99,
      screenings: [createAlwaysAvailableStreaming({ raw })],
    });
    vi.stubGlobal('MOVIES', [movie]);

    const entry = `${movie.id}_[${raw}]`;
    // Pass empty days array — always-available doesn't depend on days
    expect(resolveEntries([entry], [])).toEqual([entry]);
  });

  it('filters out non-matching entry', () => {
    vi.stubGlobal('MOVIES', []);

    const staleEntry = '999_[belgrano.Sala 6:01-01-2020 20:00]';
    expect(resolveEntries([staleEntry], [])).toEqual([]);
  });
});
