import styled from "styled-components";
import { Stylable, applyCssStyleProp } from "../../types";
import MovieLinks from "./MovieLinks";

export const Paragraph = styled.div<Stylable>`
  line-height: 20pt;
  font-size: 12pt;
  font-family: "Open Sans"; // XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX

  ${applyCssStyleProp}
`;

export const MovieHeader = styled.div`
  display: flex;
  gap: 20px;
`;

export const MovieImageWrapper = styled.div`
  display: flex;
  flex-flow: column;
  align-items: center;

  > ${MovieLinks}:not(:empty) {
    margin-top: 20px;
  }
`;

export const INLINE_HEADING_COLOR = "#eb9797";

export const InlineHeading = styled.div<Stylable>`
  font-weight: 700;
  color: ${INLINE_HEADING_COLOR};
  display: inline-block;
  margin-right: 10px;

  ${applyCssStyleProp}
`;

export const MovieTitle = styled.div`
  width: 350px;
  font-size: 20pt;
  font-weight: unset;
  margin-bottom: 10px;
`;

export const Specs = styled.div<Stylable>`
  font-size: 12pt;
  font-family: "Open Sans"; // XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX

  display: flex;
  align-items: baseline;

  ${applyCssStyleProp}
`;

export const SectionLabel = styled.div`
  display: inline-flex;
  align-items: center;

  font-size: 10pt;
  color: white;
  padding: 3px 6px;
  border-radius: 3px;
  font-family: "Open Sans";
  font-weight: 500;
  background: #b90000;
  text-transform: uppercase;
`;
