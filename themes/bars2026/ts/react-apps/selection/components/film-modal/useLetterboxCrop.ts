import { type RefObject, useLayoutEffect } from 'react';

interface BarResult {
  topPercent: number;
  bottomPercent: number;
  leftPercent: number;
  rightPercent: number;
}

const cache = new Map<string, BarResult | null>();

const BRIGHTNESS_THRESHOLD = 18; // out of 255 — catches dark-gray bars, not just pure black
const VARIANCE_THRESHOLD = 25;
const MIN_BAR_PERCENT = 3; // catch thinner bars visible on small mobile thumbnails
const MAX_ASYMMETRY = 6; // allow more asymmetric bars (subtitles/watermarks)
const MAX_SCALE = 1.35;
const SAMPLE_WIDTH = 100;

function detectLetterbox(img: HTMLImageElement): BarResult | null {
  const { naturalWidth, naturalHeight } = img;
  if (!naturalWidth || !naturalHeight) return null;

  const scale = SAMPLE_WIDTH / naturalWidth;
  const w = SAMPLE_WIDTH;
  const h = Math.round(naturalHeight * scale);
  if (h < 10) return null;

  const canvas = document.createElement('canvas');
  canvas.width = w;
  canvas.height = h;
  const ctx = canvas.getContext('2d', { willReadFrequently: true });
  if (!ctx) return null;

  ctx.drawImage(img, 0, 0, w, h);
  const { data } = ctx.getImageData(0, 0, w, h);

  function isBarRow(row: number): boolean {
    let sum = 0;
    let sumSq = 0;
    const offset = row * w * 4;
    for (let x = 0; x < w; x++) {
      const i = offset + x * 4;
      const brightness = (data[i] + data[i + 1] + data[i + 2]) / 3;
      sum += brightness;
      sumSq += brightness * brightness;
    }
    const mean = sum / w;
    const variance = sumSq / w - mean * mean;
    return mean < BRIGHTNESS_THRESHOLD && variance < VARIANCE_THRESHOLD;
  }

  function isBarCol(col: number): boolean {
    let sum = 0;
    let sumSq = 0;
    for (let y = 0; y < h; y++) {
      const i = (y * w + col) * 4;
      const brightness = (data[i] + data[i + 1] + data[i + 2]) / 3;
      sum += brightness;
      sumSq += brightness * brightness;
    }
    const mean = sum / h;
    const variance = sumSq / h - mean * mean;
    return mean < BRIGHTNESS_THRESHOLD && variance < VARIANCE_THRESHOLD;
  }

  // Scan from top
  let topRows = 0;
  for (let row = 0; row < h; row++) {
    if (!isBarRow(row)) break;
    topRows++;
  }

  // Scan from bottom
  let bottomRows = 0;
  for (let row = h - 1; row >= 0; row--) {
    if (!isBarRow(row)) break;
    bottomRows++;
  }

  // Scan from left
  let leftCols = 0;
  for (let col = 0; col < w; col++) {
    if (!isBarCol(col)) break;
    leftCols++;
  }

  // Scan from right
  let rightCols = 0;
  for (let col = w - 1; col >= 0; col--) {
    if (!isBarCol(col)) break;
    rightCols++;
  }

  const topPercent = (topRows / h) * 100;
  const bottomPercent = (bottomRows / h) * 100;
  const leftPercent = (leftCols / w) * 100;
  const rightPercent = (rightCols / w) * 100;

  const hasLetterbox =
    topPercent >= MIN_BAR_PERCENT &&
    bottomPercent >= MIN_BAR_PERCENT &&
    Math.abs(topPercent - bottomPercent) <= MAX_ASYMMETRY;

  const hasPillarbox =
    leftPercent >= MIN_BAR_PERCENT &&
    rightPercent >= MIN_BAR_PERCENT &&
    Math.abs(leftPercent - rightPercent) <= MAX_ASYMMETRY;

  if (!hasLetterbox && !hasPillarbox) return null;

  return {
    topPercent: hasLetterbox ? topPercent : 0,
    bottomPercent: hasLetterbox ? bottomPercent : 0,
    leftPercent: hasPillarbox ? leftPercent : 0,
    rightPercent: hasPillarbox ? rightPercent : 0,
  };
}

export default function useLetterboxCrop(containerRef: RefObject<HTMLElement | null>) {
  useLayoutEffect(() => {
    const container = containerRef.current;
    if (!container) return;

    const img = container.querySelector('img');
    if (!img) return;

    function apply(el: HTMLImageElement) {
      const src = el.src;
      let result: BarResult | null | undefined = cache.get(src);

      if (result === undefined) {
        result = detectLetterbox(el);
        cache.set(src, result);
      }

      if (!result) return;

      const totalV = result.topPercent + result.bottomPercent;
      const totalH = result.leftPercent + result.rightPercent;

      const vScale = totalV > 0 ? 1 / (1 - totalV / 100) : 1;
      const hScale = totalH > 0 ? 1 / (1 - totalH / 100) : 1;
      const scale = Math.min(Math.max(vScale, hScale), MAX_SCALE);

      const cy = totalV > 0 ? result.topPercent + (100 - totalV) / 2 : 50;
      const cx = totalH > 0 ? result.leftPercent + (100 - totalH) / 2 : 50;

      el.style.objectPosition = `${cx}% ${cy}%`;
      el.style.transform = `scale(${scale})`;
    }

    if (img.complete && img.naturalWidth > 0) {
      apply(img);
    } else {
      const handler = () => apply(img);
      img.addEventListener('load', handler, { once: true });
      return () => img.removeEventListener('load', handler);
    }
  }, [containerRef]);
}
