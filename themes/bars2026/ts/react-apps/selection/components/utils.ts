import Editions, { SingleEdition } from '@shared/ts/Editions';
import {
  isTraditionalScreening,
  MovieSections,
  Screening,
  ScreeningWithMovie,
} from '@shared/ts/types';

export function getVenueDisplay(
  screening: ScreeningWithMovie<Screening>,
  currentEdition: SingleEdition,
): string {
  let venueName = '';
  try {
    venueName = Editions.getVenueName(screening.venue, currentEdition);
  } catch {
    venueName = screening.venue;
  }
  const roomName = isTraditionalScreening(screening) && screening.room ? screening.room : '';
  return roomName ? `${venueName} - ${roomName}` : venueName;
}

export function getSectionLabel(screening: ScreeningWithMovie, sections: MovieSections): string {
  return sections[screening.movie.section] ?? screening.movie.section;
}
