import styled from "styled-components";
import { faCirclePlay } from "@fortawesome/free-solid-svg-icons";
import { Stylable } from "../../../types";
import { FAIcon } from "../../../utils";

export default styled(function StreamingButton({
  className,
  streamingUrl,
  isDisabled,
}: Stylable & {
  streamingUrl?: string;
  isDisabled?: boolean;
}) {
  return (
    <a
      className={className}
      target="_blank"
      rel="noopener noreferrer"
      href={streamingUrl}
    >
      <FAIcon icon={faCirclePlay} size="2x" />

      {isDisabled ? (
        <span>Link deshabilitado</span>
      ) : !streamingUrl ? (
        <span>Link no disponible</span>
      ) : (
        <span>Ver película</span>
      )}
    </a>
  );
})`
  display: flex;
  align-items: center;
  gap: 10px;

  // Reset anchor color
  color: inherit;
  font-family: "Oswald"; // XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX

  text-transform: uppercase;
  padding: 3px 14px;
  border-radius: 30px;
  letter-spacing: 1px;
  font-size: 16px;
  background: #24592e;

  > ${FAIcon} {
    margin-left: -10px;
  }

  &:hover {
    text-decoration: none;
    background: #1c8317;
  }

  ${(props) =>
    props.isDisabled || !props.streamingUrl
      ? `
        pointer-events: none;
        background: #515151;
        color: #878787;
      `
      : ""}
`;
