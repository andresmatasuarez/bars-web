import { faChevronDown } from '@fortawesome/free-solid-svg-icons';
import { CSSProperties, MouseEventHandler, ReactNode, useCallback, useMemo, useState } from 'react';
import styled from 'styled-components';

import { applyCssStyleProp, ScreeningWithMovie, Stylable } from '../../types';
import { FAIcon } from '../../utils';
import MovieScreening from '../MovieScreening';
import useStickyBetween from './useStickyBetween';

export const ALTERNATE_BG = 'rgba(30, 30, 30, 0.4)';

const Heading = styled.div<Stylable & { collapsed?: boolean }>`
  font-size: 12pt;
  color: #cecece;
  text-shadow: 2px 2px 0.1em black;
  font-family: ${(props) => props.theme.fontFamily.Oswald};

  flex: 0 0 90px;
  text-align: center;

  display: flex;
  justify-content: center;

  ${(props) => (props.collapsed ? 'z-index: 1;' : '')}

  ${applyCssStyleProp}
`;

const MoviesContainer = styled.div<{ collapsed?: boolean }>`
  font-family: ${(props) => props.theme.fontFamily.Oswald};
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

/**
 * These sensors are just empty, transparent pixels used as intersection sensors.
 * I'm doing these because using the sticky element as sensor as well causes
 * heavy flickering.
 *
 * https://stackoverflow.com/a/73436174
 */
const StickySensor = styled.div<Stylable>`
  position: absolute;
  width: 1px;
  height: 1px;
  ${applyCssStyleProp}
`;

const BASE_OFFSET = 10;

function useHeadersOffset(): number {
  return useMemo<number>(() => {
    const wpadminbarElement = document.getElementById('wpadminbar');
    const headerMenu = document.getElementById('header-menu');
    let offset = BASE_OFFSET;
    offset = offset + (wpadminbarElement ? wpadminbarElement.offsetHeight : 0);
    offset = offset + (headerMenu ? headerMenu.offsetHeight : 0);
    return offset;
  }, []);
}

export default styled(function Screenings({
  className,
  screenings,
  heading,
  startCollapsed,
}: Stylable & {
  screenings: ScreeningWithMovie[];
  heading?: ReactNode;
  startCollapsed?: boolean;
  alternateBackground?: boolean;
}) {
  const [collapsed, setCollapsed] = useState(startCollapsed);

  const headersOffset = useHeadersOffset();

  const { afterTop, afterBottom, topSensorRef, bottomSensorRef, stickyRef } =
    useStickyBetween(headersOffset);

  const toggleCollapsed = useCallback<MouseEventHandler>(() => {
    setCollapsed((previous) => !previous);
  }, [setCollapsed]);

  const styles = useMemo<CSSProperties | undefined>(() => {
    if (collapsed || !afterTop) {
      return;
    }

    if (afterBottom) {
      return { position: 'absolute', bottom: 0 };
    }

    return { position: 'fixed', top: `${headersOffset}px` };
  }, [afterTop, afterBottom, collapsed, headersOffset]);

  return (
    <div className={className} style={collapsed ? { position: 'relative' } : {}}>
      <Heading collapsed={collapsed} cssStyle="position: relative;">
        <StickySensor ref={topSensorRef} />
        <div ref={stickyRef} style={styles}>
          {heading}
        </div>
        <StickySensor ref={bottomSensorRef} cssStyle="bottom: 0;" />
      </Heading>

      <MoviesContainer collapsed={collapsed}>
        {screenings.map((screening) => (
          <MovieScreening key={`${screening.movie.id}_${screening.raw}`} screening={screening} />
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
