import { act, renderHook } from '@testing-library/react';

import {
  createAlwaysAvailableStreaming,
  createScreeningWithMovie,
  createTraditionalScreening,
} from '../data/__fixtures__/movies';
import useWatchlist, { serializeScreeningForWatchlist } from './useWatchlist';

const LOCALSTORAGE_KEY = 'bars-watchlist';

describe('serializeScreeningForWatchlist', () => {
  it('serializes a traditional screening', () => {
    const screening = createTraditionalScreening({
      raw: 'belgrano.Sala 6:11-20-2025 20:00',
    });
    const swm = createScreeningWithMovie(screening, { id: 42 });
    expect(serializeScreeningForWatchlist(swm)).toBe(
      '42_[belgrano.Sala 6:11-20-2025 20:00]',
    );
  });

  it('serializes a streaming screening', () => {
    const screening = createAlwaysAvailableStreaming({
      raw: 'streaming!flixxo:full',
    });
    const swm = createScreeningWithMovie(screening, { id: 99 });
    expect(serializeScreeningForWatchlist(swm)).toBe('99_[streaming!flixxo:full]');
  });

  it('produces format matching WatchlistEntry pattern', () => {
    const screening = createTraditionalScreening();
    const swm = createScreeningWithMovie(screening, { id: 7 });
    const result = serializeScreeningForWatchlist(swm);
    expect(result).toMatch(/^\d+_\[.+\]$/);
  });
});

describe('useWatchlist hook', () => {
  beforeEach(() => {
    localStorage.clear();
  });

  it('starts with empty watchlist when localStorage is empty', () => {
    const { result } = renderHook(() => useWatchlist());
    expect(result.current.watchlist).toEqual([]);
  });

  it('adds a screening to the watchlist', () => {
    const { result } = renderHook(() => useWatchlist());
    const swm = createScreeningWithMovie(createTraditionalScreening(), { id: 1 });

    act(() => {
      result.current.addToWatchlist(swm);
    });

    expect(result.current.watchlist).toHaveLength(1);
  });

  it('adding the same screening twice is idempotent', () => {
    const { result } = renderHook(() => useWatchlist());
    const swm = createScreeningWithMovie(createTraditionalScreening(), { id: 1 });

    act(() => {
      result.current.addToWatchlist(swm);
      result.current.addToWatchlist(swm);
    });

    expect(result.current.watchlist).toHaveLength(1);
  });

  it('removes a screening from the watchlist', () => {
    const { result } = renderHook(() => useWatchlist());
    const swm = createScreeningWithMovie(createTraditionalScreening(), { id: 1 });

    act(() => {
      result.current.addToWatchlist(swm);
    });
    act(() => {
      result.current.removeFromWatchlist(swm);
    });

    expect(result.current.watchlist).toHaveLength(0);
  });

  it('removing non-existent screening is a no-op', () => {
    const { result } = renderHook(() => useWatchlist());
    const swm = createScreeningWithMovie(createTraditionalScreening(), { id: 1 });

    act(() => {
      result.current.removeFromWatchlist(swm);
    });

    expect(result.current.watchlist).toEqual([]);
  });

  it('isAddedToWatchlist returns true after add, false after remove', () => {
    const { result } = renderHook(() => useWatchlist());
    const swm = createScreeningWithMovie(createTraditionalScreening(), { id: 1 });

    expect(result.current.isAddedToWatchlist(swm)).toBe(false);

    act(() => {
      result.current.addToWatchlist(swm);
    });
    expect(result.current.isAddedToWatchlist(swm)).toBe(true);

    act(() => {
      result.current.removeFromWatchlist(swm);
    });
    expect(result.current.isAddedToWatchlist(swm)).toBe(false);
  });

  it('isAddedToWatchlist returns false for a different screening', () => {
    const { result } = renderHook(() => useWatchlist());
    const swm1 = createScreeningWithMovie(createTraditionalScreening(), { id: 1 });
    const swm2 = createScreeningWithMovie(
      createTraditionalScreening({ raw: 'belgrano.Sala 1:11-21-2025 18:00' }),
      { id: 2 },
    );

    act(() => {
      result.current.addToWatchlist(swm1);
    });

    expect(result.current.isAddedToWatchlist(swm2)).toBe(false);
  });

  it('persists to localStorage after adding', () => {
    const { result } = renderHook(() => useWatchlist());
    const swm = createScreeningWithMovie(createTraditionalScreening(), { id: 1 });

    act(() => {
      result.current.addToWatchlist(swm);
    });

    const stored = localStorage.getItem(LOCALSTORAGE_KEY);
    expect(stored).not.toBeNull();
    expect(JSON.parse(stored!)).toContain(serializeScreeningForWatchlist(swm));
  });

  it('loads from pre-populated localStorage', () => {
    const entry = '55_[belgrano.Sala 6:11-20-2025 20:00]';
    localStorage.setItem(LOCALSTORAGE_KEY, JSON.stringify([entry]));

    const { result } = renderHook(() => useWatchlist());
    expect(result.current.watchlist).toEqual([entry]);
  });

  it('does not re-write localStorage on mount (skip initial persist)', () => {
    const original = JSON.stringify(['55_[belgrano.Sala 6:11-20-2025 20:00]']);
    localStorage.setItem(LOCALSTORAGE_KEY, original);

    renderHook(() => useWatchlist());

    expect(localStorage.getItem(LOCALSTORAGE_KEY)).toBe(original);
  });

  it('gracefully recovers from corrupt localStorage data', () => {
    localStorage.setItem(LOCALSTORAGE_KEY, 'not-json!!!');

    const { result } = renderHook(() => useWatchlist());
    expect(result.current.watchlist).toEqual([]);
  });
});
