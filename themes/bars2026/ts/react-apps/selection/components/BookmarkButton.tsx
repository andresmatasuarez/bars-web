import { BookmarkIcon } from './icons';

type Props = {
  active: boolean;
  onClick: () => void;
};

export default function BookmarkButton({ active, onClick }: Props) {
  return (
    <button
      type="button"
      onClick={(e) => {
        e.stopPropagation();
        e.preventDefault();
        onClick();
      }}
      className={`w-8 h-8 rounded-full flex items-center justify-center transition-colors cursor-pointer ${
        active
          ? 'bg-bars-primary text-white'
          : 'bg-black/40 text-white/70 hover:bg-black/60'
      }`}
      aria-label={active ? 'Quitar de mi lista' : 'Agregar a mi lista'}
      title={active ? 'Quitar de mi lista' : 'Agregar a mi lista'}
    >
      <BookmarkIcon size={16} filled={active} />
    </button>
  );
}
