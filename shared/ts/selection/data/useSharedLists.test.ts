import { act, renderHook } from '@testing-library/react';

import {
  createScreeningWithMovie,
  createTraditionalScreening,
} from '../data/__fixtures__/movies';
import type { WatchlistEntry } from './useWatchlist';
import useSharedLists from './useSharedLists';

const LOCALSTORAGE_KEY = 'bars-shared-lists';

const sampleEntries: WatchlistEntry[] = [
  '100_[belgrano.Sala 6:11-20-2025 20:00]',
  '200_[streaming!flixxo:11-21-2025]',
];

describe('useSharedLists hook', () => {
  beforeEach(() => {
    localStorage.clear();
  });

  describe('addSharedList', () => {
    it('adds a list and returns a non-null ID', () => {
      const { result } = renderHook(() => useSharedLists());

      let id: string | null = null;
      act(() => {
        id = result.current.addSharedList('My List', sampleEntries, 26);
      });

      expect(id).not.toBeNull();
      expect(result.current.sharedLists).toHaveLength(1);
      expect(result.current.sharedLists[0].name).toBe('My List');
      expect(result.current.sharedLists[0].entries).toEqual(sampleEntries);
    });

    it('does not add beyond capacity (3 per edition)', () => {
      const { result } = renderHook(() => useSharedLists());

      act(() => {
        result.current.addSharedList('List 1', sampleEntries, 26);
        result.current.addSharedList('List 2', sampleEntries, 26);
        result.current.addSharedList('List 3', sampleEntries, 26);
      });

      act(() => {
        result.current.addSharedList('List 4', sampleEntries, 26);
      });

      // State correctly rejects the 4th list
      expect(result.current.sharedLists).toHaveLength(3);
      // Verify the 4th name is not present
      expect(result.current.sharedLists.map((l) => l.name)).not.toContain('List 4');
    });

    it('different editions do not count toward each other capacity', () => {
      const { result } = renderHook(() => useSharedLists());

      act(() => {
        result.current.addSharedList('List 1', sampleEntries, 26);
        result.current.addSharedList('List 2', sampleEntries, 26);
        result.current.addSharedList('List 3', sampleEntries, 26);
      });

      let id: string | null = null;
      act(() => {
        id = result.current.addSharedList('List for 25', sampleEntries, 25);
      });

      expect(id).not.toBeNull();
      expect(result.current.sharedLists).toHaveLength(4);
    });

    it('existing lists without edition field do not block capacity for any edition', () => {
      // Pre-populate with legacy list missing edition field
      const legacyList = {
        id: 'legacy1',
        name: 'Old List',
        entries: sampleEntries,
        addedAt: Date.now(),
        // no edition field
      };
      localStorage.setItem(LOCALSTORAGE_KEY, JSON.stringify([legacyList]));

      const { result } = renderHook(() => useSharedLists());

      // Should be able to add 3 lists for edition 26 since the legacy list
      // has edition === undefined which !== 26
      let id1: string | null = null;
      let id2: string | null = null;
      let id3: string | null = null;
      act(() => {
        id1 = result.current.addSharedList('List 1', sampleEntries, 26);
        id2 = result.current.addSharedList('List 2', sampleEntries, 26);
        id3 = result.current.addSharedList('List 3', sampleEntries, 26);
      });

      expect(id1).not.toBeNull();
      expect(id2).not.toBeNull();
      expect(id3).not.toBeNull();
    });
  });

  describe('replaceSharedList', () => {
    it('replaces at same index and returns a new ID', () => {
      const { result } = renderHook(() => useSharedLists());

      let originalId: string | null = null;
      act(() => {
        originalId = result.current.addSharedList('Original', sampleEntries, 26);
      });

      let newId: string = '';
      act(() => {
        newId = result.current.replaceSharedList(originalId!, 'Replaced', sampleEntries, 26);
      });

      expect(newId).not.toBe(originalId);
      expect(result.current.sharedLists).toHaveLength(1);
      expect(result.current.sharedLists[0].name).toBe('Replaced');
      expect(result.current.sharedLists[0].id).toBe(newId);
    });

    it('appends to end when ID is non-existent', () => {
      const { result } = renderHook(() => useSharedLists());

      act(() => {
        result.current.addSharedList('First', sampleEntries, 26);
      });

      let newId: string = '';
      act(() => {
        newId = result.current.replaceSharedList('nonexistent', 'Appended', sampleEntries, 26);
      });

      expect(result.current.sharedLists).toHaveLength(2);
      expect(result.current.sharedLists[1].name).toBe('Appended');
      expect(result.current.sharedLists[1].id).toBe(newId);
    });
  });

  describe('removeSharedList', () => {
    it('removes an existing list', () => {
      const { result } = renderHook(() => useSharedLists());

      let id: string | null = null;
      act(() => {
        id = result.current.addSharedList('To Remove', sampleEntries, 26);
      });

      act(() => {
        result.current.removeSharedList(id!);
      });

      expect(result.current.sharedLists).toHaveLength(0);
    });

    it('no-op for non-existent ID', () => {
      const { result } = renderHook(() => useSharedLists());

      act(() => {
        result.current.addSharedList('Keep', sampleEntries, 26);
      });

      const lengthBefore = result.current.sharedLists.length;
      act(() => {
        result.current.removeSharedList('nonexistent');
      });

      expect(result.current.sharedLists).toHaveLength(lengthBefore);
    });
  });

  describe('getSharedListIdsForScreening', () => {
    it('returns list ID when screening is in one list', () => {
      const { result } = renderHook(() => useSharedLists());

      let id: string | null = null;
      act(() => {
        id = result.current.addSharedList('My List', sampleEntries, 26);
      });

      const screening = createScreeningWithMovie(
        createTraditionalScreening({ raw: 'belgrano.Sala 6:11-20-2025 20:00' }),
        { id: 100 },
      );

      expect(result.current.getSharedListIdsForScreening(screening)).toEqual([id]);
    });

    it('returns multiple IDs when screening is in multiple lists', () => {
      const { result } = renderHook(() => useSharedLists());

      let id1: string | null = null;
      let id2: string | null = null;
      act(() => {
        id1 = result.current.addSharedList('List A', sampleEntries, 26);
        id2 = result.current.addSharedList('List B', sampleEntries, 26);
      });

      const screening = createScreeningWithMovie(
        createTraditionalScreening({ raw: 'belgrano.Sala 6:11-20-2025 20:00' }),
        { id: 100 },
      );

      const ids = result.current.getSharedListIdsForScreening(screening);
      expect(ids).toContain(id1);
      expect(ids).toContain(id2);
    });

    it('returns empty array when screening is in no list', () => {
      const { result } = renderHook(() => useSharedLists());

      act(() => {
        result.current.addSharedList('My List', sampleEntries, 26);
      });

      const screening = createScreeningWithMovie(
        createTraditionalScreening({ raw: 'belgrano.Sala 1:11-25-2025 22:00' }),
        { id: 999 },
      );

      expect(result.current.getSharedListIdsForScreening(screening)).toEqual([]);
    });

    it('checks exact entry match (same movie, different screening is not matched)', () => {
      const { result } = renderHook(() => useSharedLists());

      act(() => {
        result.current.addSharedList('My List', sampleEntries, 26);
      });

      // Same movie ID (100) but different screening raw
      const screening = createScreeningWithMovie(
        createTraditionalScreening({ raw: 'belgrano.Sala 1:11-22-2025 18:00' }),
        { id: 100 },
      );

      expect(result.current.getSharedListIdsForScreening(screening)).toEqual([]);
    });
  });

  describe('persistence', () => {
    it('persists to localStorage after adding', () => {
      const { result } = renderHook(() => useSharedLists());

      act(() => {
        result.current.addSharedList('My List', sampleEntries, 26);
      });

      const stored = localStorage.getItem(LOCALSTORAGE_KEY);
      expect(stored).not.toBeNull();
      const parsed = JSON.parse(stored!);
      expect(parsed).toHaveLength(1);
      expect(parsed[0].name).toBe('My List');
    });

    it('loads from pre-populated localStorage', () => {
      const stored = [
        { id: 'abc', name: 'Stored', entries: sampleEntries, addedAt: Date.now(), edition: 26 },
      ];
      localStorage.setItem(LOCALSTORAGE_KEY, JSON.stringify(stored));

      const { result } = renderHook(() => useSharedLists());
      expect(result.current.sharedLists).toHaveLength(1);
      expect(result.current.sharedLists[0].name).toBe('Stored');
    });

    it('does not re-write localStorage on mount (skip initial persist)', () => {
      const original = JSON.stringify([
        { id: 'abc', name: 'Stored', entries: sampleEntries, addedAt: 1234, edition: 26 },
      ]);
      localStorage.setItem(LOCALSTORAGE_KEY, original);

      renderHook(() => useSharedLists());

      expect(localStorage.getItem(LOCALSTORAGE_KEY)).toBe(original);
    });

    it('recovers from corrupt localStorage data', () => {
      localStorage.setItem(LOCALSTORAGE_KEY, 'not-json!!!');

      const { result } = renderHook(() => useSharedLists());
      expect(result.current.sharedLists).toEqual([]);
    });

    it('filters out items with missing fields from localStorage', () => {
      const stored = [
        { id: 'valid', name: 'Valid', entries: sampleEntries, addedAt: Date.now(), edition: 26 },
        { id: 'bad', entries: sampleEntries, addedAt: Date.now() }, // missing name
        { name: 'Bad2', entries: sampleEntries, addedAt: Date.now() }, // missing id
      ];
      localStorage.setItem(LOCALSTORAGE_KEY, JSON.stringify(stored));

      const { result } = renderHook(() => useSharedLists());
      expect(result.current.sharedLists).toHaveLength(1);
      expect(result.current.sharedLists[0].id).toBe('valid');
    });

    it('starts as empty for non-array localStorage value', () => {
      localStorage.setItem(LOCALSTORAGE_KEY, '"true"');

      const { result } = renderHook(() => useSharedLists());
      expect(result.current.sharedLists).toEqual([]);
    });
  });
});
