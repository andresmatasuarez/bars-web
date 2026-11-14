import { createTraditionalScreening } from '@shared/ts/__fixtures__/movies';
import { useTimeZone, VIEWER_TIME_ZONES } from '@shared/ts/__fixtures__/timeZone';
import Editions from '@shared/ts/Editions';
import type { TraditionalScreening } from '@shared/ts/types';
import { render, screen } from '@testing-library/react';
import { createElement } from 'react';

import ScreeningCard from './ScreeningCard';

const edition27 = Editions.getByNumber(27);

// Hoarder's first screening in edition 27, as PHP sends it to the page.
const hoarderScreening = createTraditionalScreening({
  raw: 'atlas.Sala 9:11-24-2026 22:40',
  venue: 'atlas',
  room: 'sala 9',
  time: '22:40',
  isoDate: '2026-11-24T22:40:00-03:00',
});

function renderCard(screening: TraditionalScreening = hoarderScreening) {
  return render(createElement(ScreeningCard, { screening, currentEdition: edition27 }));
}

describe('ScreeningCard date box', () => {
  describe.each(VIEWER_TIME_ZONES)('viewer in %s', (timeZone) => {
    useTimeZone(timeZone);

    it('shows the Buenos Aires weekday, day and time', () => {
      renderCard();
      expect(screen.queryByText('MAR')).not.toBeNull();
      expect(screen.queryByText('24')).not.toBeNull();
      expect(screen.queryByText('22:40')).not.toBeNull();
    });
  });
});

describe('ScreeningCard Tickets button', () => {
  const withTickets = { ...hoarderScreening, ticketUrl: 'https://www.atlascines.com/' };

  beforeEach(() => {
    vi.useFakeTimers({ toFake: ['Date'] });
  });

  afterEach(() => {
    vi.useRealTimers();
  });

  it.each([
    ['one minute before the screening starts', '2026-11-24T22:39:00-03:00'],
    ['in the morning of the screening day', '2026-11-24T10:00:00-03:00'],
  ])('is a link %s', (_, now) => {
    vi.setSystemTime(new Date(now));
    renderCard(withTickets);
    expect(screen.getByText('Tickets').closest('a')).not.toBeNull();
    expect(screen.queryByText('Ya no disponible')).toBeNull();
  });

  it.each([
    ['one minute after the screening starts', '2026-11-24T22:41:00-03:00'],
    ['the next day', '2026-11-25T12:00:00-03:00'],
  ])('is turned off %s', (_, now) => {
    vi.setSystemTime(new Date(now));
    renderCard(withTickets);
    expect(screen.getByText('Tickets').closest('a')).toBeNull();
    expect(screen.queryByText('Ya no disponible')).not.toBeNull();
  });
});
