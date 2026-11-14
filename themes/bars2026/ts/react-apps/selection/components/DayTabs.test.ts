import { useTimeZone, VIEWER_TIME_ZONES } from '@shared/ts/__fixtures__/timeZone';
import Editions from '@shared/ts/Editions';
import { render, screen } from '@testing-library/react';
import { createElement } from 'react';

import DayTabs from './DayTabs';

// DayTabs reads its data from the DataProvider context; each test sets what it returns.
const mockData = vi.hoisted(() => ({ current: {} as Record<string, unknown> }));
vi.mock('../data/DataProvider', () => ({ useData: () => mockData.current }));

/** The text of each day tab, such as "JUE19". */
function renderDayTabLabels(): string[] {
  render(createElement(DayTabs));
  return screen
    .getAllByRole('button')
    .map((button) => button.textContent ?? '')
    .filter((text) => /^\D+\d+$/.test(text));
}

describe('DayTabs', () => {
  describe.each(VIEWER_TIME_ZONES)('viewer in %s', (timeZone) => {
    useTimeZone(timeZone);

    it('labels each festival day with its Buenos Aires weekday and day', () => {
      mockData.current = {
        daysWithMovies: Editions.days(Editions.getByNumber(27)),
        hasOnlineMovies: false,
        // No tab active, so the tabs do not scroll (jsdom has no scrollTo)
        activeTab: { type: 'online' },
        setActiveTab: vi.fn(),
        sharedLists: [],
      };

      expect(renderDayTabLabels()).toEqual([
        'JUE19',
        'VIE20',
        'SÁB21',
        'DOM22',
        'LUN23',
        'MAR24',
        'MIÉ25',
        'JUE26',
        'VIE27',
        'SÁB28',
        'DOM29',
      ]);
    });
  });
});
