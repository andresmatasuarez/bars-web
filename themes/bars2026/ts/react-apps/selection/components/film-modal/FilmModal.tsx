import { useMemo } from 'react';

import { Modal } from '../../../../components/modal/Modal';
import { useData } from '../../data/DataProvider';
import BlockDesktop from './BlockDesktop';
import BlockMobile from './BlockMobile';
import RegularFilmDesktop from './RegularFilmDesktop';
import RegularFilmMobile from './RegularFilmMobile';

export default function FilmModal() {
  const {
    selectedMovie,
    closeFilmModal,
    currentEdition,
    sections,
    isAddedToWatchlist,
    toggleWatchlist,
  } = useData();
  const movie = selectedMovie;

  const isBlock = movie?.isBlock ?? false;

  const containerClassName = isBlock
    ? 'relative w-full h-full lg:h-auto lg:min-h-[650px] lg:max-w-[1100px] lg:max-h-[775px] lg:rounded-bars-lg bg-bars-bg-dark flex flex-col overflow-hidden'
    : 'relative w-full h-full lg:h-auto lg:min-h-[650px] lg:max-w-[1058px] lg:max-h-[775px] lg:rounded-bars-lg bg-bars-bg-dark flex flex-col overflow-hidden';

  // Derive bookmark state at the orchestrator level so sub-components stay pure
  const firstScreening = movie?.screenings[0] ?? null;
  const screeningWithMovie = useMemo(
    () => (firstScreening && movie ? { ...firstScreening, movie } : null),
    [firstScreening, movie],
  );
  const bookmarked = screeningWithMovie ? isAddedToWatchlist(screeningWithMovie) : false;
  const onToggleBookmark = screeningWithMovie ? () => toggleWatchlist(screeningWithMovie) : null;

  const sharedProps = movie ? { currentEdition, sections, bookmarked, onToggleBookmark } : null;

  return (
    <Modal
      isOpen={movie !== null}
      onClose={closeFilmModal}
      containerClassName={containerClassName}
      ariaLabelledBy="film-modal-title"
    >
      {movie && sharedProps && (
        <>
          {/* Desktop layout */}
          <div className="hidden lg:flex lg:flex-col lg:flex-1 lg:min-h-0">
            {isBlock ? (
              <BlockDesktop movie={movie} onClose={closeFilmModal} {...sharedProps} />
            ) : (
              <RegularFilmDesktop movie={movie} onClose={closeFilmModal} {...sharedProps} />
            )}
          </div>
          {/* Mobile layout */}
          <div className="flex flex-col h-full lg:hidden">
            {isBlock ? (
              <BlockMobile movie={movie} onClose={closeFilmModal} {...sharedProps} />
            ) : (
              <RegularFilmMobile movie={movie} onClose={closeFilmModal} {...sharedProps} />
            )}
          </div>
        </>
      )}
    </Modal>
  );
}
