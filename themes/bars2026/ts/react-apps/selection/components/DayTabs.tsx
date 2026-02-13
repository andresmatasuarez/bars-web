import { dateHasPassed, serializeDate } from '@shared/ts/selection/helpers';
import { ReactNode, useCallback, useEffect, useRef, useState } from 'react';

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

function IconTab({
  tab,
  activeTab,
  activeRef,
  setActiveTab,
  icon,
  label,
}: {
  tab: ActiveTab;
  activeTab: ActiveTab;
  activeRef: React.RefObject<HTMLButtonElement | null>;
  setActiveTab: (tab: ActiveTab) => void;
  icon: ReactNode;
  label: string;
}) {
  const active = isTabActive(activeTab, tab);
  return (
    <button
      ref={active ? activeRef : undefined}
      type="button"
      onClick={() => setActiveTab(tab)}
      className={`flex flex-col items-center justify-center flex-shrink-0
        w-[49px] lg:w-[57px] h-[54px] lg:h-[60px] rounded-bars-sm transition-colors cursor-pointer
        ${active ? 'bg-bars-primary text-white' : 'bg-bars-bg-card text-bars-text-muted hover:bg-bars-bg-medium'}
      `}
    >
      {icon}
      <span className="font-body text-[7px] lg:text-[8px] font-semibold tracking-[0.5px] uppercase leading-none mt-1">
        {label}
      </span>
    </button>
  );
}

export default function DayTabs() {
  const { daysWithMovies, hasOnlineMovies, activeTab, setActiveTab } = useData();
  const scrollRef = useRef<HTMLDivElement>(null);
  const activeRef = useRef<HTMLButtonElement>(null);
  const [canScrollLeft, setCanScrollLeft] = useState(false);
  const [canScrollRight, setCanScrollRight] = useState(false);

  const updateScrollIndicators = useCallback(() => {
    const el = scrollRef.current;
    if (!el) return;
    const threshold = 2;
    setCanScrollLeft(el.scrollLeft > threshold);
    setCanScrollRight(el.scrollLeft + el.clientWidth < el.scrollWidth - threshold);
  }, []);

  useEffect(() => {
    if (activeRef.current && scrollRef.current) {
      const container = scrollRef.current;
      const el = activeRef.current;
      const left = el.offsetLeft - container.offsetLeft - container.clientWidth / 2 + el.clientWidth / 2;
      container.scrollTo({ left, behavior: 'smooth' });
      setTimeout(() => updateScrollIndicators(), 350);
    }
    updateScrollIndicators();
  }, [activeTab, updateScrollIndicators]);

  const dayTabs: { tab: ActiveTab; label: string; sublabel: string; past: boolean }[] =
    daysWithMovies.map((date) => ({
      tab: { type: 'day' as const, date },
      label: getDayAbbrev(date),
      sublabel: String(date.getDate()),
      past: dateHasPassed(date),
    }));

  const allPast = dayTabs.length > 0 && dayTabs.every((d) => d.past);

  return (
    <div className="flex pb-1 -mx-5 px-5 lg:mx-0 lg:px-0 lg:flex-wrap lg:gap-2">
      {/* Fixed left: MI LISTA + TODO (pinned on mobile) */}
      <div className="flex gap-1 lg:gap-2 flex-shrink-0 lg:contents">
        <IconTab
          tab={{ type: 'watchlist' }}
          activeTab={activeTab}
          activeRef={activeRef}
          setActiveTab={setActiveTab}
          icon={<BookmarkIcon size={18} />}
          label="MI LISTA"
        />
        <IconTab
          tab={{ type: 'all' }}
          activeTab={activeTab}
          activeRef={activeRef}
          setActiveTab={setActiveTab}
          icon={<LayoutGridIcon size={18} />}
          label="TODO"
        />
      </div>

      {/* Divider */}
      <div className="w-px bg-bars-divider self-stretch flex-shrink-0 mx-2 lg:mx-1" />

      {/* Wrapper for scroll area + gradient overlays */}
      <div className="relative min-w-0 lg:contents">
        {/* Left gradient */}
        <div
          className={`absolute left-0 top-0 bottom-0 w-6 z-10 pointer-events-none
            transition-opacity duration-200 lg:hidden
            ${canScrollLeft ? 'opacity-100' : 'opacity-0'}`}
          style={{ background: 'linear-gradient(to right, #0A0A0A, transparent)' }}
        />

        {/* Scrollable day tabs (only this section scrolls on mobile) */}
        <div
          ref={scrollRef}
          onScroll={updateScrollIndicators}
          className="flex gap-1 lg:gap-2 overflow-x-auto scrollbar-hide lg:overflow-visible lg:flex-wrap lg:contents"
          style={{ scrollbarWidth: 'none', msOverflowStyle: 'none' }}
        >
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
          {hasOnlineMovies && (
            <IconTab
              tab={{ type: 'online' }}
              activeTab={activeTab}
              activeRef={activeRef}
              setActiveTab={setActiveTab}
              icon={<MonitorPlayIcon size={18} />}
              label="ONLINE"
            />
          )}
        </div>

        {/* Right gradient */}
        <div
          className={`absolute right-0 top-0 bottom-0 w-6 z-10 pointer-events-none
            transition-opacity duration-200 lg:hidden
            ${canScrollRight ? 'opacity-100' : 'opacity-0'}`}
          style={{ background: 'linear-gradient(to left, #0A0A0A, transparent)' }}
        />
      </div>
    </div>
  );
}
