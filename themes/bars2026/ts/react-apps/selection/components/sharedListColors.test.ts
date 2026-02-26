import { getColorForList } from './sharedListColors';

describe('getColorForList', () => {
  it('returns first color for index 0', () => {
    expect(getColorForList(0)).toBe('#60A5FA');
  });

  it('wraps around after 6 colors (index 6 → first color)', () => {
    expect(getColorForList(6)).toBe('#60A5FA');
  });

  it('returns 6 unique colors for indices 0–5', () => {
    const colors = Array.from({ length: 6 }, (_, i) => getColorForList(i));
    expect(new Set(colors).size).toBe(6);
  });

  it('returns undefined for negative index (JS modulo behavior)', () => {
    expect(getColorForList(-1)).toBeUndefined();
  });
});
