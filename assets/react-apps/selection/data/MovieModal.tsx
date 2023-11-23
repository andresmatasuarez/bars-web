import Modal from "react-modal";
import styled, { css } from "styled-components";
import { faSpinner, faXmark } from "@fortawesome/free-solid-svg-icons";
import { FAIcon, ZIndexes } from "../utils";
import { Stylable, applyCssStyleProp } from "../types";

Modal.setAppElement("#react-root-selection");

const MODAL_WIDTH = "720px";
const MODAL_MIN_HEIGHT = "300px";

const customStyles = {
  content: {
    top: "50%",
    left: "50%",
    right: "auto",
    bottom: "auto",
    marginRight: "-50%",
    transform: "translate(-50%, -50%)",
    zIndex: ZIndexes.Modal,
    padding: 0,
    borderRadius: "7px",
    border: "1px solid #552f2f",
    boxShadow: "0 0 50px 10px black",
    overflow: "hidden",
  },

  overlay: { zIndex: ZIndexes.Modal, backgroundColor: "rgba(0,0,0,0.75)" },
};

export const modalScrollbar = css`
  &::-webkit-scrollbar {
    width: 8px;
    height: 8px;
  }

  &::-webkit-scrollbar-track {
    background: transparent;
  }

  &::-webkit-scrollbar-thumb {
    background-color: #552f2f;
    border-radius: 10px;
    margin-right: 5px;
  }
`;

export const modalContentStyles = css`
  width: ${MODAL_WIDTH};
  max-height: 720px;
  min-height: ${MODAL_MIN_HEIGHT};

  overflow: auto;

  ${modalScrollbar}
`;

export const ModalHeader = styled(function ModalHeader({
  className,
  onClose,
}: Stylable & { onClose: () => void }) {
  return (
    <div className={className}>
      <FAIcon icon={faXmark} onClick={onClose} size="lg" />
    </div>
  );
})`
  display: flex;
  justify-content: end;

  > ${FAIcon} {
    cursor: pointer;
    color: lightgray;

    &:hover {
      color: unset;
    }
  }

  ${applyCssStyleProp}
`;

export const ModalLoading = styled(function ModalLoading({
  className,
}: Stylable) {
  return (
    <div className={className}>
      <FAIcon icon={faSpinner} spin size="4x" />
    </div>
  );
})`
  display: flex;
  align-items: center;
  justify-content: center;

  width: ${MODAL_WIDTH};
  height: ${MODAL_MIN_HEIGHT};

  > ${FAIcon} {
    color: white;
  }

  ${applyCssStyleProp}
`;

export default function MovieModal(props: Modal.Props) {
  return (
    <Modal
      {...props}
      shouldCloseOnEsc
      shouldCloseOnOverlayClick
      contentLabel="Movie Modal"
      style={customStyles}
    />
  );
}
