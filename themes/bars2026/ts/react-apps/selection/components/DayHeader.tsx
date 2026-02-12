import { getDayAbbrev } from './dayUtils';
import { ChevronDownIcon } from './icons';

type Props = {
  date: Date;
  collapsible?: boolean;
  collapsed?: boolean;
  onToggle?: () => void;
};

export default function DayHeader({ date, collapsible, collapsed, onToggle }: Props) {
  const abbrev = getDayAbbrev(date);
  const day = date.getDate();

  return (
    <div
      className={`relative flex items-center gap-3${collapsible ? ' cursor-pointer' : ''}`}
      onClick={collapsible ? onToggle : undefined}
    >
      {collapsible && (
        <ChevronDownIcon
          size={16}
          className={`absolute -left-5 lg:-left-7 top-1/2 -translate-y-1/2 text-white/50 transition-transform duration-200${collapsed ? ' -rotate-90' : ''}`}
        />
      )}
      <span className="bg-bars-primary/25 text-white text-xs font-semibold tracking-wide uppercase px-3 py-1 rounded-full">
        {abbrev} {day}
      </span>
      <div className="flex-1 h-px bg-white/[0.08]" />
    </div>
  );
}
