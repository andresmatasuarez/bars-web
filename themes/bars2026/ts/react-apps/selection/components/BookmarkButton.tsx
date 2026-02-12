import { BookmarkIcon } from './icons';

type Props = {
  active: boolean;
  onClick: () => void;
  size?: 'sm' | 'md';
  className?: string;
};

export default function BookmarkButton({ active, onClick, size = 'sm', className }: Props) {
  const sizeClasses = size === 'md' ? 'w-10 h-10' : 'w-8 h-8';
  const iconSize = size === 'md' ? 20 : 16;

  return (
    <button
      type="button"
      onClick={(e) => {
        e.stopPropagation();
        e.preventDefault();
        onClick();
      }}
      className={`${sizeClasses} shrink-0 rounded-full flex items-center justify-center transition-all cursor-pointer active:scale-90 ${
        active
          ? 'bg-bars-primary text-white'
          : 'bg-black/40 text-white/70 hover:bg-black/60'
      }${className ? ` ${className}` : ''}`}
      aria-label={active ? 'Quitar de mi lista' : 'Agregar a mi lista'}
      title={active ? 'Quitar de mi lista' : 'Agregar a mi lista'}
    >
      <BookmarkIcon size={iconSize} filled={active} />
    </button>
  );
}
