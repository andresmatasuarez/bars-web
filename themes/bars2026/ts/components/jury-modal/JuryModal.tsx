import { useCallback, useEffect, useState } from 'react';
import { createRoot } from 'react-dom/client';

import { Modal } from '../modal/Modal';

declare const BARS_DATA: { themeUrl: string; ajaxUrl: string };

interface JuryMember {
  id: number;
  name: string;
  section: string;
  photoUrl: string;
  bio: string;
}

function CloseButton({ onClick }: { onClick: () => void }) {
  return (
    <button
      onClick={onClick}
      className="w-8 h-8 rounded-[16px] flex items-center justify-center cursor-pointer transition-colors"
      style={{ backgroundColor: 'rgba(0, 0, 0, 0.4)' }}
      onMouseEnter={(e) =>
        (e.currentTarget.style.backgroundColor = 'rgba(0, 0, 0, 0.6)')
      }
      onMouseLeave={(e) =>
        (e.currentTarget.style.backgroundColor = 'rgba(0, 0, 0, 0.4)')
      }
      aria-label="Cerrar"
    >
      <svg
        width="16"
        height="16"
        viewBox="0 0 24 24"
        fill="none"
        stroke="white"
        strokeWidth="2"
        strokeLinecap="round"
        strokeLinejoin="round"
      >
        <line x1="18" y1="6" x2="6" y2="18" />
        <line x1="6" y1="6" x2="18" y2="18" />
      </svg>
    </button>
  );
}

function PhotoPlaceholder({ className }: { className: string }) {
  return (
    <div
      className={`${className} bg-bars-bg-medium flex items-center justify-center`}
    >
      <svg
        className="w-16 h-16 text-bars-text-subtle"
        fill="currentColor"
        viewBox="0 0 24 24"
      >
        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
      </svg>
    </div>
  );
}

function DesktopContent({
  member,
  onClose,
}: {
  member: JuryMember;
  onClose: () => void;
}) {
  const halftoneUrl =
    (typeof BARS_DATA !== 'undefined' ? BARS_DATA.themeUrl : '') +
    '/resources/sala-halftone.png';

  return (
    <>
      {/* Photo banner with avatar overlay */}
      <div className="relative w-full h-[300px] shrink-0">
        {/* Halftone background image */}
        <div className="absolute inset-0 overflow-hidden">
          <img
            src={halftoneUrl}
            alt=""
            className="w-full h-full object-cover opacity-30"
          />
          {/* Gradient fade to modal background */}
          <div
            className="absolute inset-0"
            style={{
              background:
                'linear-gradient(to bottom, rgba(15,15,15,0) 0%, rgba(15,15,15,0) 30%, #0F0F0F 85%, #0F0F0F 100%)',
            }}
          />
        </div>

        {/* Close button */}
        <div className="absolute top-3 right-4 z-10">
          <CloseButton onClick={onClose} />
        </div>

        {/* Circular avatar photo */}
        <div className="absolute left-1/2 top-[20px] -translate-x-1/2 w-[260px] h-[260px] rounded-full overflow-hidden z-10">
          {member.photoUrl ? (
            <img
              src={member.photoUrl}
              alt={member.name}
              className="w-full h-full object-cover"
            />
          ) : (
            <PhotoPlaceholder className="w-full h-full" />
          )}
        </div>
      </div>

      {/* Content area */}
      <div className="flex flex-col items-center gap-4 pt-2 px-9 pb-9 min-h-0 flex-1">
        <h3 className="font-heading text-[32px] font-semibold text-white text-center">
          {member.name}
        </h3>
        <p className="text-sm font-medium text-bars-badge-text text-center">
          {member.section}
        </p>
        <div className="w-full h-px" style={{ backgroundColor: 'rgba(255, 255, 255, 0.08)' }} />
        <div className="w-full overflow-y-auto flex-1 min-h-0">
          <div
            className="text-[15px] leading-[1.7] [&_p]:mb-4 [&_p:last-child]:mb-0"
            style={{ color: 'rgba(255, 255, 255, 0.7)' }}
            dangerouslySetInnerHTML={{ __html: member.bio }}
          />
        </div>
      </div>
    </>
  );
}

function MobileContent({
  member,
  onClose,
}: {
  member: JuryMember;
  onClose: () => void;
}) {
  return (
    <>
      {/* Header bar */}
      <div
        className="flex items-center h-16 px-5 shrink-0"
        style={{ backgroundColor: 'rgba(10, 10, 10, 0.8)' }}
      >
        <button
          onClick={onClose}
          className="flex items-center gap-2 cursor-pointer"
          aria-label="Volver"
        >
          <svg
            width="20"
            height="20"
            viewBox="0 0 24 24"
            fill="none"
            stroke="white"
            strokeWidth="2"
            strokeLinecap="round"
            strokeLinejoin="round"
          >
            <polyline points="15 18 9 12 15 6" />
          </svg>
          <span className="text-sm font-medium text-white">
            Premios y Jurados
          </span>
        </button>
      </div>

      {/* Scrollable body */}
      <div className="overflow-y-auto flex-1">
        {/* Hero section */}
        <div className="flex flex-col items-center gap-4 px-5 pt-8 pb-6">
          {member.photoUrl ? (
            <div className="w-[180px] h-[180px] rounded-full overflow-hidden shrink-0">
              <img
                src={member.photoUrl}
                alt={member.name}
                className="w-full h-full object-cover"
              />
            </div>
          ) : (
            <PhotoPlaceholder className="w-[180px] h-[180px] rounded-full shrink-0" />
          )}
          <h3 className="font-heading text-[28px] font-semibold text-white text-center">
            {member.name}
          </h3>
          <p className="text-xs font-medium text-bars-badge-text text-center">
            {member.section}
          </p>
        </div>

        {/* Bio section */}
        <div className="px-5 pb-[85px]">
          <div
            className="text-sm leading-[1.7] [&_p]:mb-4 [&_p:last-child]:mb-0"
            style={{ color: 'rgba(255, 255, 255, 0.8)' }}
            dangerouslySetInnerHTML={{ __html: member.bio }}
          />
        </div>
      </div>
    </>
  );
}

function JuryModalApp() {
  const [member, setMember] = useState<JuryMember | null>(null);

  const handleOpen = useCallback((e: Event) => {
    const detail = (e as CustomEvent<JuryMember>).detail;
    setMember(detail);
  }, []);

  const handleClose = useCallback(() => setMember(null), []);

  useEffect(() => {
    document.addEventListener('jury-modal:open', handleOpen);
    return () => document.removeEventListener('jury-modal:open', handleOpen);
  }, [handleOpen]);

  return (
    <Modal
      isOpen={member !== null}
      onClose={handleClose}
      containerClassName="relative w-full h-full lg:h-auto lg:max-w-[600px] lg:min-h-[650px] lg:max-h-[730px] lg:rounded-[16px] bg-bars-bg-medium flex flex-col overflow-hidden"
    >
      {member && (
        <>
          {/* Desktop layout */}
          <div className="hidden lg:flex lg:flex-col lg:h-full lg:min-h-0">
            <DesktopContent member={member} onClose={handleClose} />
          </div>
          {/* Mobile layout */}
          <div className="flex flex-col h-full lg:hidden bg-bars-bg-dark">
            <MobileContent member={member} onClose={handleClose} />
          </div>
        </>
      )}
    </Modal>
  );
}

export function initJuryModal(): void {
  const root = document.getElementById('jury-modal-root');
  if (!root) return;
  createRoot(root).render(<JuryModalApp />);
}
