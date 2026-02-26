import { useCallback, useState } from 'react';

import { Modal } from '../../../components/modal/Modal';
import { MAX_LIST_NAME } from '../data/shareableList';
import { SaveIcon, XIcon } from './icons';

interface SaveListDialogProps {
  isOpen: boolean;
  onClose: () => void;
  onSave: (name: string) => void;
  disabled: boolean;
}

export default function SaveListDialog({ isOpen, onClose, onSave, disabled }: SaveListDialogProps) {
  const [name, setName] = useState('');

  const trimmedName = name.trim();
  const canSave = trimmedName.length > 0 && !disabled;

  const handleClose = useCallback(() => {
    setName('');
    onClose();
  }, [onClose]);

  const handleSave = useCallback(() => {
    if (!canSave) return;
    onSave(trimmedName);
    handleClose();
  }, [canSave, trimmedName, onSave, handleClose]);

  return (
    <Modal
      isOpen={isOpen}
      onClose={handleClose}
      containerClassName="relative w-[calc(100%-2rem)] max-w-sm mx-auto bg-bars-bg-card rounded-bars-lg overflow-hidden"
      ariaLabelledBy="save-list-title"
    >
      {/* Header */}
      <div className="flex items-center justify-between px-5 pt-5 pb-2">
        <h2 id="save-list-title" className="flex items-center gap-2 font-display text-lg tracking-[0.5px] text-white">
          <SaveIcon size={18} />
          Guardar mi lista
        </h2>
        <button
          type="button"
          onClick={handleClose}
          className="flex h-8 w-8 items-center justify-center rounded-full border border-bars-border-light text-bars-text-muted transition-colors hover:text-white hover:border-white/40 cursor-pointer"
        >
          <XIcon size={16} />
        </button>
      </div>

      {/* Body */}
      <div className="px-5 py-4 space-y-4">
        <p className="text-sm text-bars-text-subtle">
          Guardá una copia de tu lista personal como lista independiente. Ponele un nombre para identificarla.
        </p>
        <input
          type="text"
          value={name}
          onChange={(e) => setName(e.target.value)}
          maxLength={MAX_LIST_NAME}
          placeholder="Ej: Lista de María"
          className="w-full rounded-bars-md bg-bars-bg-dark border border-bars-border-light px-4 py-3 text-sm text-white placeholder:text-bars-text-muted/50 focus:outline-none focus:border-bars-primary transition-colors"
          autoFocus
          onKeyDown={(e) => {
            if (e.key === 'Enter' && canSave) handleSave();
          }}
        />
      </div>

      {/* Footer */}
      <div className="flex items-center gap-3 px-5 pb-5">
        <button
          type="button"
          onClick={handleClose}
          className="flex-1 rounded-bars-md px-4 py-3 text-sm font-medium transition-colors cursor-pointer border border-bars-border-light text-bars-text-muted hover:text-white hover:border-white/40"
        >
          Cancelar
        </button>
        <button
          type="button"
          onClick={handleSave}
          disabled={!canSave}
          className="flex-1 rounded-bars-md px-4 py-3 text-sm font-semibold transition-colors cursor-pointer bg-bars-primary text-white hover:bg-bars-primary/90 disabled:opacity-40 disabled:cursor-not-allowed"
        >
          Guardar
        </button>
      </div>
    </Modal>
  );
}
