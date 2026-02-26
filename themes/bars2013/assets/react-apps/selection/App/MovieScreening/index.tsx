import { MouseEventHandler, useCallback, useContext } from 'react';
import styled, { css } from 'styled-components';

import { DataContext } from '../../data/DataProvider';
import { getCurrentEdition } from '../../data/helpers';
import Editions from '../../Editions';
import {
  applyCssStyleProp,
  isTraditionalScreening,
  ScreeningWithMovie,
  Stylable,
} from '../../types';
import { DangerousHTML, Label } from '../commons';
import AddToWatchlistToggle from './AddToWatchlistToggle';

const ScreeningTime = styled(Label)`
  top: 0;
  z-index: 1;
  position: absolute;
  right: 0;

  padding-top: 2px;
  padding-bottom: 2px;
  font-size: 12pt;

  font-weight: bold;
`;

const Thumbnail = styled.div`
  position: relative;
`;

const Section = styled(Label)`
  position: absolute;
  left: 0;
  bottom: 0;
`;

const Venue = styled(Label)`
  position: absolute;
  left: 0;
  bottom: 17px;
`;

export const InfoContainer = styled.div`
  flex: 1;
  display: flex;
  flex-flow: column;

  padding: 10px;

  color: #cccccc;
`;

export const Title = styled.div`
  line-height: 14pt;
  font-size: 12pt;
`;

export const Info = styled.div`
  margin-top: 6px;
  font-size: 9pt;
`;

export const movieScreeningBoxStyles = css`
  // TODO update thumbnail size in wordpress from 160px to
  // around 10px to make it bigger?
  // Beware changing this would also affect the movies carousel
  // items inside a movie block modal
  flex: 0 0 160px;

  border: 1px solid rgba(255, 255, 255, 0.1);
  background-color: rgba(0, 0, 0, 0.5);
  border-bottom-left-radius: 15px;
  border-bottom-right-radius: 15px;

  &:hover {
    cursor: pointer;
    background-color: rgba(0, 0, 0, 1);
    border-color: rgba(255, 255, 255, 0.3);

    ${Label}, > ${InfoContainer} {
      color: white;
    }
  }
`;

export default styled(function MovieScreening({
  className,
  screening,
}: Stylable & {
  screening: ScreeningWithMovie;
}) {
  const { sections, isAddedToWatchlist, addToWatchlist, removeFromWatchlist, openMovieModal } =
    useContext(DataContext);

  const isAdded = isAddedToWatchlist(screening);

  const handleToggleFromWatchlist = useCallback<MouseEventHandler>(
    (ev) => {
      ev.stopPropagation();

      if (isAdded) {
        removeFromWatchlist(screening);
      } else {
        addToWatchlist(screening);
      }
    },
    [screening, addToWatchlist, removeFromWatchlist, isAdded],
  );

  const currentEdition = getCurrentEdition();

  let venueName = Editions.getVenueName(screening.venue, currentEdition);

  if (isTraditionalScreening(screening)) {
    // If 'room' was entered and there is only one venue for this edition,
    // then display only the room.
    venueName =
      screening.room && Object.keys(Editions.venues(currentEdition)).length === 1
        ? screening.room
        : venueName;
  }

  return (
    <div className={className} onClick={() => openMovieModal(screening.movie)}>
      {isTraditionalScreening(screening) && <ScreeningTime>{screening.time}</ScreeningTime>}

      <Thumbnail>
        <Venue>{venueName}</Venue>
        <Section>{sections[screening.movie.section]}</Section>

        <DangerousHTML html={screening.movie.thumbnail} />
      </Thumbnail>

      <InfoContainer>
        <Title>{screening.movie.title}</Title>
        <Info>{screening.movie.info}</Info>

        <AddToWatchlistToggle
          isAdded={isAdded}
          onClick={handleToggleFromWatchlist}
          cssStyle="align-self: flex-end;"
        />
      </InfoContainer>
    </div>
  );
})`
  display: flex;
  flex-flow: column;

  position: relative;

  ${movieScreeningBoxStyles}

  ${applyCssStyleProp}
`;
