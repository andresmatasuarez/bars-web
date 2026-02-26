import {
  createAlwaysAvailableStreaming,
  createRegularStreaming,
  createTraditionalScreening,
} from './data/__fixtures__/movies';
import {
  isRegularStreamingScreening,
  isScreeningAlwaysAvailable,
  isStreamingScreening,
  isTraditionalScreening,
} from './types';

describe('isStreamingScreening', () => {
  it('returns true for always-available streaming', () => {
    expect(isStreamingScreening(createAlwaysAvailableStreaming())).toBe(true);
  });

  it('returns true for regular streaming', () => {
    expect(isStreamingScreening(createRegularStreaming())).toBe(true);
  });

  it('returns false for traditional screening', () => {
    expect(isStreamingScreening(createTraditionalScreening())).toBe(false);
  });
});

describe('isTraditionalScreening', () => {
  it('returns true for traditional screening', () => {
    expect(isTraditionalScreening(createTraditionalScreening())).toBe(true);
  });

  it('returns false for always-available streaming', () => {
    expect(isTraditionalScreening(createAlwaysAvailableStreaming())).toBe(false);
  });

  it('returns false for regular streaming', () => {
    expect(isTraditionalScreening(createRegularStreaming())).toBe(false);
  });
});

describe('isScreeningAlwaysAvailable', () => {
  it('returns true for always-available streaming', () => {
    expect(isScreeningAlwaysAvailable(createAlwaysAvailableStreaming())).toBe(true);
  });

  it('returns false for regular streaming', () => {
    expect(isScreeningAlwaysAvailable(createRegularStreaming())).toBe(false);
  });

  it('returns false for traditional screening', () => {
    expect(isScreeningAlwaysAvailable(createTraditionalScreening())).toBe(false);
  });
});

describe('isRegularStreamingScreening', () => {
  it('returns true for regular streaming', () => {
    expect(isRegularStreamingScreening(createRegularStreaming())).toBe(true);
  });

  it('returns false for always-available streaming', () => {
    expect(isRegularStreamingScreening(createAlwaysAvailableStreaming())).toBe(false);
  });

  it('returns false for traditional screening', () => {
    expect(isRegularStreamingScreening(createTraditionalScreening())).toBe(false);
  });
});
