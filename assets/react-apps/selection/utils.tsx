import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import styled from "styled-components";
import { Stylable, applyCssStyleProp } from "./types";

export const ZIndexes = {
  Modal: 9999,
  SelectDropdown: 8888,
} as const satisfies Record<string, number>;

export const FAIcon = styled(FontAwesomeIcon)`
  ${applyCssStyleProp}
`;

export function explodeCommaSeparated(str: string) {
  return str.split(",").map((s) => s.trim());
}
