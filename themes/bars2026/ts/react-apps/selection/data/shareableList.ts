import type { WatchlistEntry } from '@shared/ts/useWatchlist';

export const SHARE_PARAM = 'list';
export const MAX_LIST_NAME = 30;
export const MAX_ENTRIES = 200;
export const MAX_SHARED_LISTS = 3;
const MAX_PAYLOAD_SIZE = 50_000; // 50KB

const ENTRY_PATTERN = /^\d+_\[.+\]$/;

export function sanitizeListName(raw: string): string {
  // Strip HTML tags
  let name = raw.replace(/<[^>]*>/g, '');
  // Allow word chars (letters, digits, underscore), spaces, and common punctuation
  name = name.replace(/[^\w\s.,!?'"\-()áéíóúñüÁÉÍÓÚÑÜ]/g, '');
  name = name.trim();
  return name.slice(0, MAX_LIST_NAME);
}

export function validateEntry(entry: unknown): entry is WatchlistEntry {
  return typeof entry === 'string' && ENTRY_PATTERN.test(entry);
}

function toBase64Url(str: string): string {
  const bytes = new TextEncoder().encode(str);
  let binary = '';
  for (let i = 0; i < bytes.length; i++) {
    binary += String.fromCharCode(bytes[i]);
  }
  return btoa(binary)
    .replace(/\+/g, '-')
    .replace(/\//g, '_')
    .replace(/=+$/, '');
}

function fromBase64Url(b64url: string): string {
  let b64 = b64url.replace(/-/g, '+').replace(/_/g, '/');
  // Restore padding
  while (b64.length % 4 !== 0) b64 += '=';
  const binary = atob(b64);
  const bytes = new Uint8Array(binary.length);
  for (let i = 0; i < binary.length; i++) {
    bytes[i] = binary.charCodeAt(i);
  }
  return new TextDecoder().decode(bytes);
}

export type DecodedShareableList = {
  name: string;
  entries: WatchlistEntry[];
};

export function encodeShareableList(name: string, entries: WatchlistEntry[]): string {
  const sanitized = sanitizeListName(name);
  const capped = entries.slice(0, MAX_ENTRIES);
  const payload = JSON.stringify({ n: sanitized, e: capped });
  return toBase64Url(payload);
}

export function decodeShareableList(encoded: string): DecodedShareableList | null {
  try {
    if (encoded.length > MAX_PAYLOAD_SIZE) return null;

    const json = fromBase64Url(encoded);
    const parsed: unknown = JSON.parse(json);

    if (
      typeof parsed !== 'object' ||
      parsed === null ||
      !('n' in parsed) ||
      !('e' in parsed)
    ) {
      return null;
    }

    const obj = parsed as { n: unknown; e: unknown };

    if (typeof obj.n !== 'string' || !Array.isArray(obj.e)) {
      return null;
    }

    const name = sanitizeListName(obj.n);
    if (name.length === 0) return null;

    const entries = (obj.e as unknown[])
      .filter(validateEntry)
      .slice(0, MAX_ENTRIES);

    if (entries.length === 0) return null;

    return { name, entries };
  } catch {
    return null;
  }
}

export function buildShareUrl(name: string, entries: WatchlistEntry[]): string {
  const encoded = encodeShareableList(name, entries);
  const params = new URLSearchParams(window.location.search);
  // Remove film modal param to avoid opening a modal on the recipient's side
  params.delete('f');
  params.set(SHARE_PARAM, encoded);
  return window.location.origin + window.location.pathname + '?' + params.toString();
}
