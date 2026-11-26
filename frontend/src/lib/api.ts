const API_BASE = process.env.NEXT_PUBLIC_API_URL || 'http://127.0.0.1:8000/api';

export async function fetchApi(endpoint: string, options: RequestInit = {}) {
  const url = `${API_BASE}${endpoint.startsWith('/') ? endpoint : `/${endpoint}`}`;
  try {
    const res = await fetch(url, {
      ...options,
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        ...options.headers,
      },
      next: { revalidate: 10 },
    });
    if (!res.ok) {
      throw new Error(`API error: ${res.status} ${res.statusText}`);
    }
    const json = await res.json();
    return json.data;
  } catch (error) {
    console.error(`Fetch failed for ${url}:`, error);
    return null;
  }
}

export function formatImageUrl(path?: string | null) {
  if (!path) return 'https://shrawaneffects.com/storage/default.jpg';
  if (path.startsWith('http://') || path.startsWith('https://')) return path;
  return `http://127.0.0.1:8000/storage/${path.replace(/^\/+/, '')}`;
}
