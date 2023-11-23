import React, { useContext } from 'react';
import styled from 'styled-components';
import Select from 'react-select';
import { Stylable } from '../../types';
import { DataContext, MovieListType } from '../../data/DataProvider';
import { faEye } from '@fortawesome/free-regular-svg-icons';
import { FAIcon, ZIndexes } from '../../utils';

const FILTERS_BG_COLOR = '#55353b';

const TogglesContainer = styled.div`
  font-family: 'Oswald'; // XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX

  display: flex;

  border: 2px solid ${FILTERS_BG_COLOR};
  border-radius: 10px;
  background: ${FILTERS_BG_COLOR};
`;

const Toggle = styled.div<{ isActive?: boolean }>`
  cursor: pointer;
  display: flex;
  align-items: center;

  padding: 10px;
  border-radius: 8px;

  color: lightgray;
  &:hover {
    color: white;
  }

  ${(props) =>
    props.isActive
      ? `
          pointer-events: none;
          color: white;
          background: #c14949;
        `
      : ''}
`;

export default styled(function Filters({ className }: Stylable) {
  const {
    selectedSection,
    sectionOptions,
    changeSection,

    currentMovieListType,
    changeMovieListType,
  } = useContext(DataContext);

  return (
    <div className={className}>
      <Select
        isClearable
        value={selectedSection}
        onChange={changeSection}
        options={sectionOptions}
        // Fix to dropdown menu being under components that define z-index greater than 0
        // https://stackoverflow.com/a/63898744
        menuPortalTarget={document.body}
        styles={{
          menuPortal: (base) => ({ ...base, zIndex: ZIndexes.SelectDropdown }),
          container: (base) => ({
            ...base,
            flex: '0 0 250px',
            borderRadius: '10px',
          }),
          control: (base, state) => ({
            ...base,
            fontFamily: 'Oswald', // XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
            borderColor: FILTERS_BG_COLOR,
            border: `2px solid ${FILTERS_BG_COLOR}`,
            height: '100%',

            // Disables blue 'active' outline
            boxShadow: 'none',

            '&:hover': {
              borderColor: FILTERS_BG_COLOR,
            },
          }),
          menu: (base, state) => ({
            ...base,
            fontFamily: 'Oswald', // XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
            marginTop: '0px',
            border: `2px solid ${FILTERS_BG_COLOR}`,
            borderBottomLeftRadius: '10px',
            borderBottomRightRadius: '10px',
            overflow: 'hidden',
          }),
          option: (base, state) => ({
            ...base,
            backgroundColor: state.isSelected ? '#c14949' : base.backgroundColor,
          }),
        }}
      />

      <TogglesContainer>
        <Toggle
          isActive={currentMovieListType === MovieListType.ALL}
          onClick={() => {
            changeMovieListType(MovieListType.ALL);
          }}
        >
          Todas las películas
        </Toggle>

        <Toggle
          isActive={currentMovieListType === MovieListType.WATCHLIST}
          onClick={() => {
            changeMovieListType(MovieListType.WATCHLIST);
          }}
        >
          Mi selección
          <FAIcon
            icon={faEye}
            beat={currentMovieListType === MovieListType.WATCHLIST}
            style={{ marginLeft: '6px' }}
          />
        </Toggle>
      </TogglesContainer>
    </div>
  );
})`
  display: flex;
  justify-content: space-between;

  margin: 40px 15px 20px 15px;
`;
