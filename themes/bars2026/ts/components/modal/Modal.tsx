import React, { useCallback, useEffect, useRef, useState } from 'react';
import { createPortal } from 'react-dom';

const TRANSITION_DURATION_MS = 200;

interface ModalProps {
  isOpen: boolean;
  onClose: () => void;
  children: React.ReactNode;
  containerClassName?: string;
  ariaLabelledBy?: string;
}

export function Modal({ isOpen, onClose, children, containerClassName, ariaLabelledBy }: ModalProps) {
  const overlayRef = useRef<HTMLDivElement>(null);
  const containerRef = useRef<HTMLDivElement>(null);
  const previouslyFocusedRef = useRef<HTMLElement | null>(null);
  const [mounted, setMounted] = useState(false);
  const [visible, setVisible] = useState(false);

  // Orchestrate mount/visible from isOpen
  useEffect(() => {
    let raf1: number, raf2: number, timeout: ReturnType<typeof setTimeout>;
    if (isOpen) {
      previouslyFocusedRef.current = document.activeElement as HTMLElement | null;
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

  // Restore focus when modal unmounts
  useEffect(() => {
    if (!mounted && previouslyFocusedRef.current) {
      previouslyFocusedRef.current.focus();
      previouslyFocusedRef.current = null;
    }
  }, [mounted]);

  // Focus the container when it becomes visible
  useEffect(() => {
    if (visible && containerRef.current) {
      containerRef.current.focus();
    }
  }, [visible]);

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

  // Focus trap: constrain Tab/Shift+Tab within the modal
  const handleKeyDown = useCallback((e: React.KeyboardEvent) => {
    if (e.key !== 'Tab') return;
    const container = containerRef.current;
    if (!container) return;

    const focusable = container.querySelectorAll<HTMLElement>(
      'a[href], button:not([disabled]), textarea, input, select, [tabindex]:not([tabindex="-1"])',
    );
    if (focusable.length === 0) {
      e.preventDefault();
      return;
    }

    const first = focusable[0];
    const last = focusable[focusable.length - 1];

    if (e.shiftKey) {
      if (document.activeElement === first || document.activeElement === container) {
        e.preventDefault();
        last.focus();
      }
    } else {
      if (document.activeElement === last) {
        e.preventDefault();
        first.focus();
      }
    }
  }, []);

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

  return createPortal(
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
        ref={containerRef}
        role="dialog"
        aria-modal="true"
        aria-labelledby={ariaLabelledBy}
        tabIndex={-1}
        onKeyDown={handleKeyDown}
        className={
          containerClassName ??
          'relative w-full h-full lg:max-w-[600px] lg:max-h-[700px] lg:rounded-2xl bg-[#0F0F0F] flex flex-col overflow-hidden'
        }
        style={{
          opacity: visible ? 1 : 0,
          transform: visible ? 'scale(1)' : 'scale(0.97)',
          transition: `opacity ${TRANSITION_DURATION_MS}ms ${visible ? 'ease-out' : 'ease-in'}, transform ${TRANSITION_DURATION_MS}ms ${visible ? 'ease-out' : 'ease-in'}`,
          outline: 'none',
        }}
      >
        {children}
      </div>
    </div>,
    document.body,
  );
}
