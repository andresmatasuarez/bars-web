import styled from 'styled-components';

import scratchSrc from '../../../images/scratch.png';
import { applyCssStyleProp, Stylable } from '../types';

export const Divider = styled.div`
  opacity: 0.4;
  height: 1px;
  background-image: url(${scratchSrc});
`;

export const Label = styled.div`
  background-color: rgb(180, 10, 0);
  padding: 0 5px;

  text-shadow: 1px 1px 0.1em black;
  font-size: 7.5pt;
  color: #cccccc;
`;

export const DangerousHTML = styled.div.attrs<
  Stylable & {
    html: string;
  }
>((props) => ({
  dangerouslySetInnerHTML: { __html: props.html },
}))`
  ${applyCssStyleProp}
`;
