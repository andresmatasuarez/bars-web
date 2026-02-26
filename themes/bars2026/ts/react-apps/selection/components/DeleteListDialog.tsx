import { Modal } from '../../../components/modal/Modal';
import { TrashIcon, XIcon } from './icons';

interface DeleteListDialogProps {
  isOpen: boolean;
  onClose: () => void;
  listName: string;
  onConfirm: () => void;
}

export default function DeleteListDialog({
  isOpen,
  onClose,
  listName,
  onConfirm,
}: DeleteListDialogProps) {
  return (
    <Modal
      isOpen={isOpen}
      onClose={onClose}
      containerClassName="relative w-[calc(100%-2rem)] max-w-sm mx-auto bg-bars-bg-card rounded-bars-lg overflow-hidden"
      ariaLabelledBy="delete-list-title"
    >
      {/* Header */}
      <div className="flex items-center justify-between px-5 pt-5 pb-2">
        <h2
          id="delete-list-title"
          className="flex items-center gap-2 font-display text-lg tracking-[0.5px] text-white"
        >
          <TrashIcon size={18} />
          Eliminar lista
        </h2>
        <button
          type="button"
          onClick={onClose}
          className="flex h-8 w-8 items-center justify-center rounded-full border border-bars-border-light text-bars-text-muted transition-colors hover:text-white hover:border-white/40 cursor-pointer"
        >
          <XIcon size={16} />
        </button>
      </div>

      {/* Body */}
      <div className="px-5 py-4">
        <p className="text-sm text-bars-text-subtle">
          ¿Estás seguro de que querés eliminar la lista &quot;{listName}&quot;?
        </p>
      </div>

      {/* Footer */}
      <div className="flex items-center gap-3 px-5 pb-5">
        <button
          type="button"
          onClick={onClose}
          className="flex-1 rounded-bars-md px-4 py-3 text-sm font-medium transition-colors cursor-pointer border border-bars-border-light text-bars-text-muted hover:text-white hover:border-white/40"
        >
          Cancelar
        </button>
        <button
          type="button"
          onClick={onConfirm}
          className="flex-1 rounded-bars-md px-4 py-3 text-sm font-semibold transition-colors cursor-pointer bg-bars-primary text-white hover:bg-bars-primary/90"
        >
          Eliminar
        </button>
      </div>
    </Modal>
  );
}
