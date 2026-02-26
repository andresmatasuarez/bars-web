import { BookmarkIcon, SearchXIcon } from './icons';

type Props = {
  type: 'empty-watchlist' | 'no-results';
};

export default function EmptyState({ type }: Props) {
  if (type === 'empty-watchlist') {
    return (
      <div className="flex flex-col items-center justify-center py-20 text-center">
        <BookmarkIcon size={48} className="text-bars-icon-empty mb-4" />
        <h3 className="font-heading text-2xl text-bars-text-primary mb-2">Tu lista está vacía</h3>
        <p className="text-sm text-bars-text-subtle max-w-xs">
          Agregá películas tocando el ícono de etiqueta en cada película para armar tu propia
          grilla.
        </p>
      </div>
    );
  }

  return (
    <div className="flex flex-col items-center justify-center py-20 text-center">
      <SearchXIcon size={48} className="text-bars-icon-empty mb-4" />
      <h3 className="font-heading text-2xl text-bars-text-primary mb-2">Sin resultados</h3>
      <p className="text-sm text-bars-text-subtle max-w-xs">
        No se encontraron películas con los filtros seleccionados. Probá cambiando la sección o el
        día.
      </p>
    </div>
  );
}
