import {
  createScreeningWithMovie,
  createTraditionalScreening,
} from '@shared/ts/__fixtures__/movies';
import { useTimeZone, VIEWER_TIME_ZONES } from '@shared/ts/__fixtures__/timeZone';
import { groupByDayAndTimeSlot } from '@shared/ts/screeningHelpers';
import { render } from '@testing-library/react';
import { createElement } from 'react';

import DayHeader from './DayHeader';

describe('DayHeader', () => {
  describe.each(VIEWER_TIME_ZONES)('viewer in %s', (timeZone) => {
    useTimeZone(timeZone);

    it('labels a day group with its Buenos Aires weekday and day', () => {
      // Hoarder's first screening: Tuesday 24 at 22:40 in Buenos Aires
      const screening = createScreeningWithMovie(
        createTraditionalScreening({ isoDate: '2026-11-24T22:40:00-03:00', time: '22:40' }),
      );
      const [group] = groupByDayAndTimeSlot([screening]);

      const { container } = render(createElement(DayHeader, { date: group.date }));
      expect(container.textContent).toBe('MAR 24');
    });
  });
});
