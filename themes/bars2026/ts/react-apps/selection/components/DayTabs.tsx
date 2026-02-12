import { dateHasPassed, serializeDate } from '@shared/ts/selection/helpers';
import { useEffect, useRef } from 'react';

import { ActiveTab, useData } from '../data/DataProvider';
import { getDayAbbrev } from './dayUtils';
import { BookmarkIcon, LayoutGridIcon, MonitorPlayIcon } from './icons';

function isTabActive(activeTab: ActiveTab, tab: ActiveTab): boolean {
  if (activeTab.type !== tab.type) return false;
  if (activeTab.type === 'day' && tab.type === 'day') {
    return (
      serializeDate(activeTab.date).split('T')[0] ===
      serializeDate(tab.date).split('T')[0]
    );
  }
  return true;
}

export default function DayTabs() {
  const { daysWithMovies, hasOnlineMovies, activeTab, setActiveTab } = useData();
  const scrollRef = useRef<HTMLDivElement>(null);
  const activeRef = useRef<HTMLButtonElement>(null);

  useEffect(() => {
    if (activeRef.current && scrollRef.current) {
      const container = scrollRef.current;
      const el = activeRef.current;
      const left = el.offsetLeft - container.offsetLeft - container.clientWidth / 2 + el.clientWidth / 2;
      container.scrollTo({ left, behavior: 'smooth' });
    }
  }, [activeTab]);

  const dayTabs: { tab: ActiveTab; label: string; sublabel: string; past: boolean }[] =
    daysWithMovies.map((date) => ({
      tab: { type: 'day' as const, date },
      label: getDayAbbrev(date),
      sublabel: String(date.getDate()),
      past: dateHasPassed(date),
    }));

  const allPast = dayTabs.length > 0 && dayTabs.every((d) => d.past);

  return (
    <div
      ref={scrollRef}
      className="flex gap-2 overflow-x-auto scrollbar-hide pb-1 -mx-5 px-5 lg:mx-0 lg:px-0 lg:overflow-visible lg:flex-wrap"
      style={{ scrollbarWidth: 'none', msOverflowStyle: 'none' }}
    >
      {/* TODO tab */}
      {(() => {
        const allTab: ActiveTab = { type: 'all' };
        const active = isTabActive(activeTab, allTab);
        return (
          <button
            ref={active ? activeRef : undefined}
            type="button"
            onClick={() => setActiveTab(allTab)}
            className={`flex flex-col items-center justify-center flex-shrink-0
              w-[49px] lg:w-[57px] h-[54px] lg:h-[60px] rounded-bars-sm transition-colors cursor-pointer
              ${active ? 'bg-bars-primary text-white' : 'bg-bars-bg-card text-bars-text-muted hover:bg-bars-bg-medium'}
            `}
          >
            <LayoutGridIcon size={18} />
            <span className="font-body text-[7px] lg:text-[8px] font-semibold tracking-[0.5px] uppercase leading-none mt-1">
              TODO
            </span>
          </button>
        );
      })()}

      {/* Divider */}
      <div className="w-px bg-bars-divider self-stretch flex-shrink-0 mx-1" />

      {dayTabs.map(({ tab, label, sublabel, past }) => {
        const active = isTabActive(activeTab, tab);
        return (
          <button
            key={tab.type === 'day' ? serializeDate(tab.date) : tab.type}
            ref={active ? activeRef : undefined}
            type="button"
            onClick={() => setActiveTab(tab)}
            className={`flex flex-col items-center justify-center flex-shrink-0
              w-[49px] lg:w-[57px] h-[54px] lg:h-[60px] rounded-bars-sm transition-colors cursor-pointer
              ${active ? 'bg-bars-primary text-white' : 'bg-bars-bg-card text-bars-text-muted hover:bg-bars-bg-medium'}
              ${past && !active && !allPast ? 'opacity-50' : ''}
            `}
          >
            <span className="font-body text-[9px] font-semibold tracking-[1px] uppercase leading-none">
              {label}
            </span>
            <span className="font-display text-[24px] leading-none mt-0.5">
              {sublabel}
            </span>
          </button>
        );
      })}

      {/* Online tab */}
      {hasOnlineMovies && (() => {
        const onlineTab: ActiveTab = { type: 'online' };
        const active = isTabActive(activeTab, onlineTab);
        return (
          <button
            ref={active ? activeRef : undefined}
            type="button"
            onClick={() => setActiveTab(onlineTab)}
            className={`flex flex-col items-center justify-center flex-shrink-0
              w-[49px] lg:w-[57px] h-[54px] lg:h-[60px] rounded-bars-sm transition-colors cursor-pointer
              ${active ? 'bg-bars-primary text-white' : 'bg-bars-bg-card text-bars-text-muted hover:bg-bars-bg-medium'}
            `}
          >
            <MonitorPlayIcon size={18} />
            <span className="font-body text-[7px] lg:text-[8px] font-semibold tracking-[0.5px] uppercase leading-none mt-1">
              ONLINE
            </span>
          </button>
        );
      })()}

      {/* Divider */}
      <div className="w-px bg-bars-divider self-stretch flex-shrink-0 mx-1" />

      {/* Watchlist tab */}
      {(() => {
        const watchlistTab: ActiveTab = { type: 'watchlist' };
        const active = isTabActive(activeTab, watchlistTab);
        return (
          <button
            ref={active ? activeRef : undefined}
            type="button"
            onClick={() => setActiveTab(watchlistTab)}
            className={`flex flex-col items-center justify-center flex-shrink-0
              w-[49px] lg:w-[57px] h-[54px] lg:h-[60px] rounded-bars-sm transition-colors cursor-pointer
              ${active ? 'bg-bars-primary text-white' : 'bg-bars-bg-card text-bars-text-muted hover:bg-bars-bg-medium'}
            `}
          >
            <BookmarkIcon size={18} />
            <span className="font-body text-[7px] lg:text-[8px] font-semibold tracking-[0.5px] uppercase leading-none mt-1">
              MI LISTA
            </span>
          </button>
        );
      })()}
    </div>
  );
}
