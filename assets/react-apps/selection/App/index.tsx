import { faHeartCrack } from '@fortawesome/free-solid-svg-icons';
import { useContext } from 'react';
import styled from 'styled-components';

import { dateHasPassed } from '../../helpers';
import { DataContext } from '../data/DataProvider';
import { isLatestEdition } from '../data/helpers';
import { ScreeningsByDay, Stylable } from '../types';
import { FAIcon } from '../utils';
import { Divider } from './commons';
import Filters from './Filters';
import Screenings from './Screenings';
import DayHeading from './Screenings/DayHeading';

function noScreeningsForAnyDay(screeningsByDay: ScreeningsByDay): boolean {
  const count = Object.entries(screeningsByDay).reduce(
    (accum, [, screenings]) => accum + screenings.length,
    0,
  );

  return count === 0;
}

const NoScreenings = styled.div`
  display: flex;
  flex-flow: column;
  gap: 15px;

  color: gray;
  padding: 50px;
  font-family: ${(props) => props.theme.fontFamily.Oswald};
  text-align: center;
  font-size: 30px;
`;

export default styled(function App({ className }: Stylable) {
  const { screeningsByDay, alwaysAvailableScreenings } = useContext(DataContext);

  const noScreeningsAtAll =
    alwaysAvailableScreenings.length === 0 && noScreeningsForAnyDay(screeningsByDay);

  return (
    <div className={className}>
      <Filters />

      <Divider />

      {noScreeningsAtAll && (
        <NoScreenings>
          <FAIcon icon={faHeartCrack} size="3x" />
          No se encontraron películas para los filtros seleccionados
        </NoScreenings>
      )}

      {alwaysAvailableScreenings.length > 0 && (
        <>
          <Screenings
            heading={
              <>
                MIRALAS
                <br />
                CUALQUIER
                <br />
                DÍA
              </>
            }
            screenings={alwaysAvailableScreenings}
          />

          <Divider />
        </>
      )}

      {Object.entries(screeningsByDay).map(([dateStr, screenings], index) => {
        const screeningDay = new Date(dateStr);

        const startCollapsed = isLatestEdition() && dateHasPassed(screeningDay);

        if (screenings.length === 0) {
          return null;
        }

        return (
          <>
            <Screenings
              heading={<DayHeading date={screeningDay} />}
              screenings={screenings}
              startCollapsed={startCollapsed}
              alternateBackground={index % 2 === 0}
            />

            <Divider />
          </>
        );
      })}
    </div>
  );
})`
  padding: 10px 0;
`;
