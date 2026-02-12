export default function BackHeader({ onClose }: { onClose: () => void }) {
  return (
    <div className="flex items-center h-16 px-5 shrink-0 bg-[rgba(10,10,10,0.8)]">
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
        <span className="text-sm font-medium text-white">Programación</span>
      </button>
    </div>
  );
}
