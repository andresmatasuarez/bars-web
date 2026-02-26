import { WatchlistEntry } from '@shared/ts/useWatchlist';
import { useCallback, useMemo, useRef, useState } from 'react';

import { useData } from '../data/DataProvider';
import { buildShareUrl } from '../data/shareableList';
import { BookmarkIcon, SaveIcon, ShareIcon, XIcon } from './icons';
import { useShareCopy } from './ShareButton';
import { getColorForList } from './sharedListColors';

/** Count unique movie IDs from watchlist/shared-list entry strings that exist in the current edition. */
function countMovies(entries: string[]): number {
  const currentMovieIds = new Set(window.MOVIES.map((m) => m.id));
  const ids = new Set<number>();
  for (const entry of entries) {
    const match = entry.match(/^(\d+)_/);
    if (match) {
      const movieId = Number(match[1]);
      if (currentMovieIds.has(movieId)) ids.add(movieId);
    }
  }
  return ids.size;
}

interface ListSubTabsProps {
  onSharePersonalList: () => void;
  onSavePersonalList: () => void;
}

export default function ListSubTabs({ onSharePersonalList, onSavePersonalList }: ListSubTabsProps) {
  const { sharedLists, listSubTab, setListSubTab, watchlist, requestDeleteSharedList } = useData();
  const { share, showTooltip } = useShareCopy();

  // Track the clicked share icon's position for the fixed tooltip
  const tooltipPosRef = useRef<{ x: number; y: number } | null>(null);
  const [, forceRender] = useState(0);

  const captureAndShare = useCallback(
    (el: Element, name: string, entries: WatchlistEntry[]) => {
      const rect = el.getBoundingClientRect();
      tooltipPosRef.current = { x: rect.left + rect.width / 2, y: rect.top };
      forceRender((n) => n + 1);
      share(buildShareUrl(name, entries), `Lista de ${name} - BARS`);
    },
    [share],
  );

  const personalCount = useMemo(() => countMovies(watchlist), [watchlist]);

  const sharedCounts = useMemo(
    () => sharedLists.map((list) => countMovies(list.entries)),
    [sharedLists],
  );

  return (
    <div className="relative mt-4">
      <div
        className="flex items-center gap-2 overflow-x-auto scrollbar-hide"
        style={{ scrollbarWidth: 'none', msOverflowStyle: 'none' }}
      >
        {/* Personal watchlist sub-tab */}
        <button
          type="button"
          onClick={() => setListSubTab('personal')}
          className={`inline-flex items-center gap-1.5 rounded-bars-md border px-3.5 py-2 text-xs font-medium transition-colors cursor-pointer flex-shrink-0
            ${
              listSubTab === 'personal'
                ? 'bg-bars-primary border-bars-primary text-white'
                : 'bg-bars-bg-card border-transparent text-bars-text-muted hover:text-white'
            }
          `}
        >
          <BookmarkIcon size={12} filled={listSubTab === 'personal'} />
          Mi lista
          <span className="rounded-full bg-white/20 py-0.5 px-2 text-[11px] leading-none font-semibold text-white tabular-nums">
            {personalCount}
          </span>
          <span
            role="button"
            tabIndex={0}
            onClick={(e) => {
              e.stopPropagation();
              onSharePersonalList();
            }}
            onKeyDown={(e) => {
              if (e.key === 'Enter' || e.key === ' ') {
                e.stopPropagation();
                onSharePersonalList();
              }
            }}
            className="ml-0.5 inline-flex items-center justify-center rounded-full hover:bg-white/20 transition-colors w-5 h-5"
            title="Compartir lista"
          >
            <ShareIcon size={14} />
          </span>
          <span
            role="button"
            tabIndex={0}
            onClick={(e) => {
              e.stopPropagation();
              onSavePersonalList();
            }}
            onKeyDown={(e) => {
              if (e.key === 'Enter' || e.key === ' ') {
                e.stopPropagation();
                onSavePersonalList();
              }
            }}
            className={`inline-flex items-center justify-center rounded-full transition-colors w-5 h-5 -mr-1${
              watchlist.length === 0 ? ' opacity-40 pointer-events-none' : ' hover:bg-white/20'
            }`}
            title="Guardar como nueva lista"
          >
            <SaveIcon size={14} />
          </span>
        </button>

        {/* Shared list sub-tabs */}
        {sharedLists.map((list, index) => {
          const color = getColorForList(index);
          const isActive = listSubTab === list.id;
          return (
            <button
              key={list.id}
              type="button"
              onClick={() => setListSubTab(list.id)}
              className={`inline-flex items-center gap-1.5 rounded-bars-md border px-3.5 py-2 text-xs font-medium transition-colors cursor-pointer flex-shrink-0
                ${
                  isActive
                    ? 'text-white'
                    : 'bg-bars-bg-card border-transparent text-bars-text-muted hover:text-white'
                }
              `}
              style={isActive ? { borderColor: color, backgroundColor: `${color}20` } : undefined}
            >
              <span
                className="inline-block w-2.5 h-2.5 rounded-full flex-shrink-0"
                style={{ backgroundColor: color }}
              />
              {list.name}
              <span className="rounded-full bg-white/20 py-0.5 px-2 text-[11px] leading-none font-semibold text-white tabular-nums">
                {sharedCounts[index]}
              </span>
              <span
                role="button"
                tabIndex={0}
                onClick={(e) => {
                  e.stopPropagation();
                  captureAndShare(e.currentTarget, list.name, list.entries);
                }}
                onKeyDown={(e) => {
                  if (e.key === 'Enter' || e.key === ' ') {
                    e.stopPropagation();
                    captureAndShare(e.currentTarget, list.name, list.entries);
                  }
                }}
                className="ml-0.5 inline-flex items-center justify-center rounded-full hover:bg-white/20 transition-colors w-5 h-5"
                title={`Compartir "${list.name}"`}
              >
                <ShareIcon size={14} />
              </span>
              <span
                role="button"
                tabIndex={0}
                onClick={(e) => {
                  e.stopPropagation();
                  requestDeleteSharedList(list.id);
                }}
                onKeyDown={(e) => {
                  if (e.key === 'Enter' || e.key === ' ') {
                    e.stopPropagation();
                    requestDeleteSharedList(list.id);
                  }
                }}
                className="inline-flex items-center justify-center rounded-full hover:bg-white/20 transition-colors w-5 h-5 -mr-1"
                title={`Eliminar "${list.name}"`}
              >
                <XIcon size={14} />
              </span>
            </button>
          );
        })}
      </div>
      {showTooltip && tooltipPosRef.current && (
        <span
          className="fixed rounded bg-black/80 px-2 py-1 text-[11px] text-white pointer-events-none animate-fade-in z-50"
          style={{
            left: tooltipPosRef.current.x,
            top: tooltipPosRef.current.y - 8,
            transform: 'translate(-50%, -100%)',
          }}
        >
          Enlace copiado
        </span>
      )}
    </div>
  );
}
