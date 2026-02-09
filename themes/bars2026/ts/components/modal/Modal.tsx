import React, { useCallback, useEffect, useRef } from 'react';

interface ModalProps {
  isOpen: boolean;
  onClose: () => void;
  children: React.ReactNode;
  containerClassName?: string;
}

export function Modal({ isOpen, onClose, children, containerClassName }: ModalProps) {
  const overlayRef = useRef<HTMLDivElement>(null);

  const handleEscape = useCallback(
    (e: KeyboardEvent) => {
      if (e.key === 'Escape') onClose();
    },
    [onClose],
  );

  useEffect(() => {
    if (isOpen) {
      document.body.style.overflow = 'hidden';
      document.addEventListener('keydown', handleEscape);
    }
    return () => {
      document.body.style.overflow = '';
      document.removeEventListener('keydown', handleEscape);
    };
  }, [isOpen, handleEscape]);

  if (!isOpen) return null;

  const handleOverlayClick = (e: React.MouseEvent) => {
    if (e.target === overlayRef.current) onClose();
  };

  return (
    <div
      ref={overlayRef}
      onClick={handleOverlayClick}
      className="fixed inset-0 z-50 flex items-center justify-center lg:p-4"
      style={{ backgroundColor: 'rgba(10, 10, 10, 0.6)' }}
    >
      <div
        className={
          containerClassName ??
          'relative w-full h-full lg:max-w-[600px] lg:max-h-[700px] lg:rounded-2xl bg-[#0F0F0F] flex flex-col overflow-hidden'
        }
      >
        {children}
      </div>
    </div>
  );
}
