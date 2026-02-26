import integerToRoman from './integerToRoman';

describe('integerToRoman', () => {
  it('converts 1 to I', () => {
    expect(integerToRoman(1)).toBe('I');
  });

  it('converts 4 to IV', () => {
    expect(integerToRoman(4)).toBe('IV');
  });

  it('converts 9 to IX', () => {
    expect(integerToRoman(9)).toBe('IX');
  });

  it('converts 26 to XXVI', () => {
    expect(integerToRoman(26)).toBe('XXVI');
  });

  it('converts 400 to CD', () => {
    expect(integerToRoman(400)).toBe('CD');
  });

  it('converts 3999 to MMMCMXCIX', () => {
    expect(integerToRoman(3999)).toBe('MMMCMXCIX');
  });
});
