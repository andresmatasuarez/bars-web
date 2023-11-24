import { faEye, faEyeSlash } from '@fortawesome/free-regular-svg-icons';
import { MouseEventHandler, useCallback, useState } from 'react';
import styled from 'styled-components';

import { applyCssStyleProp, Stylable } from '../../types';
import { FAIcon } from '../../utils';

export const IS_ADDED_COLOR = '#ffbe46';

function WatchlistIcon({ isAdded, isHovered }: { isAdded?: boolean; isHovered?: boolean }) {
  if (isAdded) {
    if (isHovered) {
      return <FAIcon icon={faEyeSlash} color={IS_ADDED_COLOR} title="Remover de mi selección" />;
    }

    return <FAIcon icon={faEye} color={IS_ADDED_COLOR} />;
  }

  if (isHovered) {
    return <FAIcon icon={faEye} beat title="Agregar a mi selección" />;
  }

  return <FAIcon icon={faEye} color="#413f3f" />;
}

export default styled(function AddToWatchlistToggle({
  className,
  isAdded,
  onClick,
}: Stylable & {
  isAdded?: boolean;
  onClick: MouseEventHandler;
}) {
  const [isHovered, setIsHovered] = useState(false);

  const handleMouseOver = useCallback(() => setIsHovered(true), []);
  const handleMouseOut = useCallback(() => setIsHovered(false), []);

  return (
    <div
      className={className}
      onClick={onClick}
      onMouseOver={handleMouseOver}
      onMouseOut={handleMouseOut}
    >
      <WatchlistIcon isAdded={isAdded} isHovered={isHovered} />
    </div>
  );
})`
  margin-top: auto;

  cursor: pointer;
  font-size: 2em;

  ${applyCssStyleProp}
`;
