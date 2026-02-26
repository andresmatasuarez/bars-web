import { useMemo, useState } from 'react';

import { useData } from '../data/DataProvider';
import DayHeader from './DayHeader';
import DayTabs from './DayTabs';
import DeleteListDialog from './DeleteListDialog';
import EmptyState from './EmptyState';
import FilmModal from './film-modal';
import FilmCard from './FilmCard';
import FilterPills from './FilterPills';
import ListSubTabs from './ListSubTabs';
import MobileFilterButton from './MobileFilterButton';
import MobileFilterModal from './MobileFilterModal';
import OverwriteListDialog from './OverwriteListDialog';
import ReplaceListDialog from './ReplaceListDialog';
import SaveListDialog from './SaveListDialog';
import { getColorForList } from './sharedListColors';
import ShareListDialog from './ShareListDialog';
import TimeSlot from './TimeSlot';
import { getSectionLabel, getVenueDisplay } from './utils';
import WatchlistStreamingSection from './WatchlistStreamingSection';
import WebViewBanner from './WebViewBanner';

type SharedListColor = { name: string; color: string };

export default function App() {
  const {
    screeningsForActiveTab,
    dayGroups,
    alwaysAvailableScreenings,
    isAddedToWatchlist,
    toggleWatchlist,
    activeTab,
    currentEdition,
    sections,
    openFilmModal,
    watchlist,
    resolvedWatchlist,
    sharedLists,
    editionSharedLists,
    emptySharedListIds,
    getSharedListIdsForScreening,
    deleteConfirmation,
    confirmDeleteSharedList,
    cancelDeleteSharedList,
    pendingSharedList,
    replaceDialogOpen,
    handleReplace,
    cancelReplace,
    overwriteDialogOpen,
    overwriteTargetName,
    confirmOverwrite,
    cancelOverwrite,
    saveDialogOpen,
    closeSaveDialog,
    openSaveDialog,
    savePersonalAsShared,
    listSubTab,
    isInActiveSubTabList,
  } = useData();

  const [collapsedDays, setCollapsedDays] = useState<Set<string>>(new Set());
  const [filterModalOpen, setFilterModalOpen] = useState(false);
  const [shareDialogOpen, setShareDialogOpen] = useState(false);

  const toggleDay = (date: Date) => {
    const key = date.toISOString();
    setCollapsedDays((prev) => {
      const next = new Set(prev);
      next.has(key) ? next.delete(key) : next.add(key);
      return next;
    });
  };

  // Build a map from list ID to color for active shared lists
  const sharedListColorMap = useMemo(() => {
    const map = new Map<string, { name: string; color: string }>();
    sharedLists.forEach((list, index) => {
      map.set(list.id, { name: list.name, color: getColorForList(index) });
    });
    return map;
  }, [sharedLists]);

  // Helper to compute shared list colors for a screening
  const getSharedColorsForScreening = useMemo(() => {
    if (sharedLists.length === 0) return () => [] as SharedListColor[];
    return (screening: Parameters<typeof getSharedListIdsForScreening>[0]): SharedListColor[] => {
      const ids = getSharedListIdsForScreening(screening);
      if (ids.length === 0) return [];
      const colors: SharedListColor[] = [];
      for (const id of ids) {
        const info = sharedListColorMap.get(id);
        if (info) colors.push(info);
      }
      return colors;
    };
  }, [sharedLists.length, getSharedListIdsForScreening, sharedListColorMap]);

  const isWatchlist = activeTab.type === 'watchlist';
  const isAll = activeTab.type === 'all';
  const isOnline = activeTab.type === 'online';
  const needsDayGroups = isWatchlist || isAll;
  const isPersonalSubTab = listSubTab === 'personal';

  const timeSlots = Array.from(screeningsForActiveTab.entries());
  const hasTimeSlotResults = timeSlots.some(([, screenings]) => screenings.length > 0);

  const hasWatchlistStreaming =
    isWatchlist && alwaysAvailableScreenings.some((s) => isInActiveSubTabList(s));

  const hasResults = isWatchlist
    ? dayGroups.length > 0 || hasWatchlistStreaming
    : isAll
      ? dayGroups.length > 0 || alwaysAvailableScreenings.length > 0
      : hasTimeSlotResults;

  return (
    <div className="pt-6 lg:pt-8 font-body">
      {/* WebView warning banner */}
      <WebViewBanner />

      {/* Day tabs */}
      <DayTabs />

      {/* Sub-tabs for personal/shared lists (only on watchlist tab) */}
      {isWatchlist && (
        <ListSubTabs
          onSharePersonalList={() => setShareDialogOpen(true)}
          onSavePersonalList={openSaveDialog}
        />
      )}

      {/* Filter pills – desktop only */}
      <div className="hidden lg:block mt-6">
        <FilterPills />
      </div>

      {/* Filter button + modal – mobile only */}
      <div className="lg:hidden mt-5">
        <MobileFilterButton onOpen={() => setFilterModalOpen(true)} />
      </div>
      <MobileFilterModal isOpen={filterModalOpen} onClose={() => setFilterModalOpen(false)} />

      <ShareListDialog
        isOpen={shareDialogOpen}
        onClose={() => setShareDialogOpen(false)}
        watchlist={resolvedWatchlist}
      />

      <SaveListDialog
        isOpen={saveDialogOpen}
        onClose={closeSaveDialog}
        onSave={savePersonalAsShared}
        disabled={watchlist.length === 0}
      />

      <ReplaceListDialog
        isOpen={replaceDialogOpen}
        onClose={cancelReplace}
        pendingList={pendingSharedList}
        existingLists={editionSharedLists}
        emptyListIds={emptySharedListIds}
        onReplace={handleReplace}
      />

      <DeleteListDialog
        isOpen={deleteConfirmation !== null}
        onClose={cancelDeleteSharedList}
        listName={deleteConfirmation?.name ?? ''}
        onConfirm={confirmDeleteSharedList}
      />

      <OverwriteListDialog
        isOpen={overwriteDialogOpen}
        onClose={cancelOverwrite}
        listName={overwriteTargetName}
        onConfirm={confirmOverwrite}
      />

      {/* Content */}
      <div className="mt-6 lg:mt-8 space-y-8 lg:space-y-10">
        {!hasResults ? (
          <EmptyState
            type={
              isWatchlist && isPersonalSubTab && watchlist.length === 0
                ? 'empty-watchlist'
                : 'no-results'
            }
          />
        ) : isOnline ? (
          <div>
            <p className="text-sm text-bars-link-accent/70 italic mb-5 border-l-2 border-bars-link-accent/25 pl-3">
              Podés ver las siguientes películas por streaming cualquier día, a cualquier hora,
              durante el transcurso del festival.
            </p>
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-5">
              {timeSlots.flatMap(([, screenings]) =>
                screenings.map((screening) => (
                  <FilmCard
                    key={`${screening.movie.id}-${screening.raw}`}
                    screening={screening}
                    sectionLabel={getSectionLabel(screening, sections)}
                    venueDisplay={getVenueDisplay(screening, currentEdition)}
                    bookmarked={isAddedToWatchlist(screening)}
                    onToggleWatchlist={() => toggleWatchlist(screening)}
                    onOpenModal={() => openFilmModal(screening.movie)}
                    sharedListColors={getSharedColorsForScreening(screening)}
                  />
                )),
              )}
            </div>
          </div>
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

      {/* Film detail modal */}
      <FilmModal />
    </div>
  );
}
