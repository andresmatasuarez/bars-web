import EDITIONS from '@shared/editions.json';

/**
 * Each edition can have two colors taken from its poster (`colors` in editions.json).
 * The site uses them for text: links, dates and titles on hover. These tests check
 * that every color stays easy to read on the site's dark backgrounds.
 */

// The lightest background these colors appear on: cards (--color-bars-bg-card in
// themes/bars2026/css/main.css). A color readable here is readable on the page
// background too, which is darker.
const CARD_BACKGROUND = '#1a1a1a';

// Text on a color 1 background when there are poster colors (--color-bars-on-edition-accent
// in themes/bars2026/php/header.php).
const DARK_TEXT = '#0a0a0a';

// WCAG AA minimum contrast for normal-size text.
const MIN_CONTRAST = 4.5;

const HEX_COLOR = /^#[0-9a-fA-F]{6}$/;

function relativeLuminance(hex: string): number {
  const [r, g, b] = [1, 3, 5].map((i) => {
    const channel = parseInt(hex.slice(i, i + 2), 16) / 255;
    return channel <= 0.03928 ? channel / 12.92 : Math.pow((channel + 0.055) / 1.055, 2.4);
  });
  return 0.2126 * r + 0.7152 * g + 0.0722 * b;
}

function contrastRatio(a: string, b: string): number {
  const [darker, lighter] = [relativeLuminance(a), relativeLuminance(b)].sort((x, y) => x - y);
  return (lighter + 0.05) / (darker + 0.05);
}

const editionsWithColors = EDITIONS.flatMap((edition) =>
  'colors' in edition && edition.colors ? [{ year: edition.year, colors: edition.colors }] : [],
);

describe('edition colors', () => {
  it('rejects the dark brand red, which is too dark to read as text', () => {
    expect(contrastRatio('#8b0000', CARD_BACKGROUND)).toBeLessThan(MIN_CONTRAST);
  });

  it.each(editionsWithColors)('edition $year has two colors written as #rrggbb', ({ colors }) => {
    expect(colors.linkAccent).toMatch(HEX_COLOR);
    expect(colors.badgeText).toMatch(HEX_COLOR);
  });

  it.each(editionsWithColors)(
    'edition $year has colors that are easy to read on the dark background',
    ({ colors }) => {
      expect(contrastRatio(colors.linkAccent, CARD_BACKGROUND)).toBeGreaterThanOrEqual(
        MIN_CONTRAST,
      );
      expect(contrastRatio(colors.badgeText, CARD_BACKGROUND)).toBeGreaterThanOrEqual(MIN_CONTRAST);
    },
  );

  // Selected tabs and filters on the programme page fill with color 1 and show dark
  // text on it (--color-bars-on-edition-accent, set in header.php).
  it.each(editionsWithColors)(
    'edition $year has a link color that dark text is easy to read on',
    ({ colors }) => {
      expect(contrastRatio(DARK_TEXT, colors.linkAccent)).toBeGreaterThanOrEqual(MIN_CONTRAST);
    },
  );
});
