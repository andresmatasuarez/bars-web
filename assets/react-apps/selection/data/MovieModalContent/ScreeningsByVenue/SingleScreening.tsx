import { MouseEventHandler, useCallback, useContext } from 'react';
import styled from 'styled-components';

import { dateHasPassed, getDayName, getDayNumber } from '../../../../helpers';
import AddToWatchlistToggle from '../../../App/MovieScreening/AddToWatchlistToggle';
import { isStreamingScreening,ScreeningWithMovie, Stylable } from '../../../types';
import { DataContext } from '../../DataProvider';

const DayName = styled.div`
  text-transform: capitalize;
`;

const DayNumber = styled.div`
  font-size: 23pt;
`;

const ScreeningTime = styled.div``;

const Room = styled.div`
  text-transform: uppercase;
`;

const Disableable = styled.div<{ isDisabled?: boolean }>`
  ${(props) => (props.isDisabled ? 'color: gray;' : '')}
`;

export default styled(function SingleScreening({
  className,
  screening,
}: Stylable & { screening: ScreeningWithMovie }) {
  const { isAddedToWatchlist, addToWatchlist, removeFromWatchlist } = useContext(DataContext);

  const isAdded = isAddedToWatchlist(screening);

  const handleToggleFromWatchlist = useCallback<MouseEventHandler>(() => {
    if (isAdded) {
      removeFromWatchlist(screening);
    } else {
      addToWatchlist(screening);
    }
  }, [screening, addToWatchlist, removeFromWatchlist, isAdded]);

  if (isStreamingScreening(screening)) {
    return (
      <Disableable
        className={className}
        isDisabled={!!screening.isoDate && dateHasPassed(new Date(screening.isoDate))}
      >
        {screening.isoDate && (
          <>
            <DayName>{getDayName(new Date(screening.isoDate))}</DayName>
            <DayNumber>{getDayNumber(new Date(screening.isoDate))}</DayNumber>
          </>
        )}
      </Disableable>
    );
  }

  return (
    <Disableable className={className} isDisabled={dateHasPassed(new Date(screening.isoDate))}>
      <DayName>{getDayName(new Date(screening.isoDate))}</DayName>
      <DayNumber>{getDayNumber(new Date(screening.isoDate))}</DayNumber>

      <ScreeningTime>{screening.time}</ScreeningTime>
      {screening.room && <Room>{screening.room}</Room>}

      <AddToWatchlistToggle
        isAdded={isAddedToWatchlist(screening)}
        onClick={handleToggleFromWatchlist}
      />
    </Disableable>
  );
})`
  display: flex;
  flex-flow: column;
  align-items: center;

  font-family: ${(props) => props.theme.fontFamily.Oswald};
`;
