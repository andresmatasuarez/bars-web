import { useState } from 'react';

import { useData } from '../data/DataProvider';
import DayHeader from './DayHeader';
import DayTabs from './DayTabs';
import EmptyState from './EmptyState';
import FilterPills from './FilterPills';
import TimeSlot from './TimeSlot';
import WatchlistStreamingSection from './WatchlistStreamingSection';

export default function App() {
  const {
    screeningsForActiveTab,
    dayGroups,
    alwaysAvailableScreenings,
    isAddedToWatchlist,
    activeTab,
  } = useData();

  const [collapsedDays, setCollapsedDays] = useState<Set<string>>(new Set());

  const toggleDay = (date: Date) => {
    const key = date.toISOString();
    setCollapsedDays((prev) => {
      const next = new Set(prev);
      next.has(key) ? next.delete(key) : next.add(key);
      return next;
    });
  };

  const isWatchlist = activeTab.type === 'watchlist';
  const isAll = activeTab.type === 'all';
  const needsDayGroups = isWatchlist || isAll;

  const timeSlots = Array.from(screeningsForActiveTab.entries());
  const hasTimeSlotResults = timeSlots.some(([, screenings]) => screenings.length > 0);

  const hasWatchlistStreaming = isWatchlist &&
    alwaysAvailableScreenings.some((s) => isAddedToWatchlist(s));

  const hasResults = isWatchlist
    ? dayGroups.length > 0 || hasWatchlistStreaming
    : isAll
      ? dayGroups.length > 0 || alwaysAvailableScreenings.length > 0
      : hasTimeSlotResults;

  return (
    <div className="pt-6 lg:pt-8 font-body">
      {/* Day tabs */}
      <DayTabs />

      {/* Filter pills */}
      <div className="mt-5 lg:mt-6">
        <FilterPills />
      </div>

      {/* Content */}
      <div className="mt-6 lg:mt-8 space-y-8 lg:space-y-10">
        {!hasResults ? (
          <EmptyState
            type={isWatchlist ? 'empty-watchlist' : 'no-results'}
          />
        ) : needsDayGroups ? (
          <>
            {dayGroups.map((group) => {
              const collapsed = collapsedDays.has(group.date.toISOString());
              return (
                <div key={group.date.toISOString()}>
                  <DayHeader
                    date={group.date}
                    collapsible
                    collapsed={collapsed}
                    onToggle={() => toggleDay(group.date)}
                  />
                  {!collapsed && (
                    <div className="space-y-6 mt-6">
                      {Array.from(group.timeSlots.entries()).map(([time, screenings]) =>
                        screenings.length > 0 ? (
                          <TimeSlot key={time} time={time} screenings={screenings} hideDivider />
                        ) : null,
                      )}
                    </div>
                  )}
                </div>
              );
            })}
            <WatchlistStreamingSection filterByWatchlist={isWatchlist} />
          </>
        ) : (
          timeSlots.map(([time, screenings]) =>
            screenings.length > 0 ? (
              <TimeSlot key={time} time={time} screenings={screenings} />
            ) : null,
          )
        )}
      </div>
    </div>
  );
}
