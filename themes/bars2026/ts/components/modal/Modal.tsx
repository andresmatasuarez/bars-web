import React, { useCallback, useEffect, useRef, useState } from 'react';

const TRANSITION_DURATION_MS = 200;

interface ModalProps {
  isOpen: boolean;
  onClose: () => void;
  children: React.ReactNode;
  containerClassName?: string;
}

export function Modal({ isOpen, onClose, children, containerClassName }: ModalProps) {
  const overlayRef = useRef<HTMLDivElement>(null);
  const [mounted, setMounted] = useState(false);
  const [visible, setVisible] = useState(false);

  // Orchestrate mount/visible from isOpen
  useEffect(() => {
    let raf1: number, raf2: number, timeout: ReturnType<typeof setTimeout>;
    if (isOpen) {
      setMounted(true);
      // Double rAF ensures browser paints opacity-0 before transitioning to opacity-1
      raf1 = requestAnimationFrame(() => {
        raf2 = requestAnimationFrame(() => {
          setVisible(true);
        });
      });
    } else {
      setVisible(false);
      // Safety timeout in case transitionend doesn't fire
      timeout = setTimeout(() => setMounted(false), TRANSITION_DURATION_MS + 50);
    }
    return () => {
      cancelAnimationFrame(raf1);
      cancelAnimationFrame(raf2);
      clearTimeout(timeout);
    };
  }, [isOpen]);

  // Scroll lock + escape key — gated on mounted so it stays active during close transition
  const handleEscape = useCallback(
    (e: KeyboardEvent) => {
      if (e.key === 'Escape') onClose();
    },
    [onClose],
  );

  useEffect(() => {
    if (mounted) {
      document.body.style.overflow = 'hidden';
      document.addEventListener('keydown', handleEscape);
    }
    return () => {
      document.body.style.overflow = '';
      document.removeEventListener('keydown', handleEscape);
    };
  }, [mounted, handleEscape]);

  // Unmount after close animation completes
  const handleTransitionEnd = useCallback(
    (e: React.TransitionEvent) => {
      if (e.target === overlayRef.current && !isOpen) {
        setMounted(false);
      }
    },
    [isOpen],
  );

  if (!mounted) return null;

  const handleOverlayClick = (e: React.MouseEvent) => {
    if (e.target === overlayRef.current) onClose();
  };

  return (
    <div
      ref={overlayRef}
      onClick={handleOverlayClick}
      onTransitionEnd={handleTransitionEnd}
      className="fixed inset-0 z-50 flex items-center justify-center lg:p-4"
      style={{
        backgroundColor: 'rgba(10, 10, 10, 0.6)',
        opacity: visible ? 1 : 0,
        transition: `opacity ${TRANSITION_DURATION_MS}ms ${visible ? 'ease-out' : 'ease-in'}`,
      }}
    >
      <div
        className={
          containerClassName ??
          'relative w-full h-full lg:max-w-[600px] lg:max-h-[700px] lg:rounded-2xl bg-[#0F0F0F] flex flex-col overflow-hidden'
        }
        style={{
          opacity: visible ? 1 : 0,
          transform: visible ? 'scale(1)' : 'scale(0.97)',
          transition: `opacity ${TRANSITION_DURATION_MS}ms ${visible ? 'ease-out' : 'ease-in'}, transform ${TRANSITION_DURATION_MS}ms ${visible ? 'ease-out' : 'ease-in'}`,
        }}
      >
        {children}
      </div>
    </div>
  );
}
