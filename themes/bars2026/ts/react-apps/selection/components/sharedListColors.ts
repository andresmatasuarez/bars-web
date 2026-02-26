const PALETTE = [
  '#60A5FA', // blue
  '#34D399', // green
  '#FBBF24', // amber
  '#F472B6', // pink
  '#A78BFA', // violet
  '#22D3EE', // cyan
];

export function getColorForList(index: number): string {
  return PALETTE[index % PALETTE.length];
}
