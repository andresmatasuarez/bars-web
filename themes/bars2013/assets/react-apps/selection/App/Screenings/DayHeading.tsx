import styled from 'styled-components';

import { getDayName, getDayNumber } from '../../../helpers';
import { applyCssStyleProp,Stylable } from '../../types';

const DayName = styled.div`
  font-size: 12pt;
  text-transform: capitalize;
`;

const DayNumber = styled.div`
  font-size: 36px;
`;

export default styled(function DayHeading({ className, date }: Stylable & { date: Date }) {
  return (
    <div className={className}>
      <DayName>{getDayName(date)}</DayName>
      <DayNumber>{getDayNumber(date)}</DayNumber>
    </div>
  );
})`
  font-family: ${(props) => props.theme.fontFamily.Oswald};
  color: #cecece;
  text-shadow: 2px 2px 0.1em black;

  ${applyCssStyleProp}
`;
