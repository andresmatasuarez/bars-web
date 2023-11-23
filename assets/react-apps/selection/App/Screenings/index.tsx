import React, { MouseEventHandler, ReactNode, useCallback, useState } from 'react';
import styled from 'styled-components';
import { ScreeningWithMovie, Stylable, applyCssStyleProp } from '../../types';
import MovieScreening from '../MovieScreening';
import { faChevronDown } from '@fortawesome/free-solid-svg-icons';
import { FAIcon } from '../../utils';

export const ALTERNATE_BG = 'rgba(30, 30, 30, 0.4)';

const Heading = styled.div<{ collapsed?: boolean }>`
  font-size: 12pt;
  color: #cecece;
  text-shadow: 2px 2px 0.1em black;
  font-family: 'Oswald'; // XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX

  flex: 0 0 90px;
  text-align: center;

  // sticky day feature
  position: relative;

  ${(props) => (props.collapsed ? 'z-index: 1;' : '')}
`;

const MoviesContainer = styled.div<{ collapsed?: boolean }>`
  font-family: 'Oswald'; // XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
  color: white;

  flex: 1;
  display: flex;
  flex-wrap: wrap;
  column-gap: 10px;
  row-gap: 20px;

  // arbitrary value for the transition. I guess it's quite unlikely
  // this container would be bigger than 2000px.
  max-height: 2000px;

  transition: max-height 0.1s;

  ${(props) =>
    props.collapsed
      ? `
      overflow: hidden;
      max-height: 90px;
  `
      : ''}
`;

const CollapseToggle = styled.div.attrs<{ collapsed?: boolean }>({
  children: <FAIcon icon={faChevronDown} />,
})`
  cursor: pointer;
  font-size: 1.3em;
  color: #cecece;
  margin-right: 20px;

  > ${FAIcon} {
    transition: transform 0.1s;
  }

  ${(props) =>
    props.collapsed
      ? `
        z-index: 1;
        > ${FAIcon} {
          transform: rotate(-90deg);
        }
      `
      : ''}
`;

const ShadowOverlay = styled.div`
  position: relative;

  content: '';
  position: absolute;
  left: 0;
  right: 0;
  top: 0;
  bottom: 0;

  background: rgb(0, 0, 0);
  background: linear-gradient(0deg, rgba(0, 0, 0, 1) 10%, rgba(0, 0, 0, 0) 100%);
`;

export default styled(function Screenings({
  className,
  screenings,
  heading,
  startCollapsed,
  alternateBackground,
}: Stylable & {
  screenings: ScreeningWithMovie[];
  heading?: ReactNode;
  startCollapsed?: boolean;
  alternateBackground?: boolean;
}) {
  const [collapsed, setCollapsed] = useState(startCollapsed);

  const toggleCollapsed = useCallback<MouseEventHandler>(() => {
    setCollapsed((previous) => !previous);
  }, [setCollapsed]);

  return (
    <div className={className} style={collapsed ? { position: 'relative' } : {}}>
      <Heading collapsed={collapsed}>{heading}</Heading>

      <MoviesContainer collapsed={collapsed}>
        {screenings.map((screening) => (
          <MovieScreening screening={screening} />
        ))}
      </MoviesContainer>

      <CollapseToggle collapsed={collapsed} onClick={toggleCollapsed} />

      {collapsed && <ShadowOverlay />}
    </div>
  );
})`
  display: flex;
  padding: 15px 0;

  padding-top: 20px;

  ${(props) =>
    props.alternateBackground
      ? `
        background-color: ${ALTERNATE_BG};
      `
      : `
        ${MovieScreening} {
          background-color: ${ALTERNATE_BG};
        }
      `}

  ${applyCssStyleProp}
`;
