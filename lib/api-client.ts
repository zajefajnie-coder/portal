// Klient API dla eksportu statycznego
// Użyj tego zamiast bezpośredniego połączenia z bazą danych w komponentach klienckich

const API_URL = process.env.NEXT_PUBLIC_API_URL || '/api';

export async function fetchLooks(): Promise<any[]> {
  try {
    const response = await fetch(`${API_URL}/looks`);
    if (!response.ok) throw new Error('Failed to fetch looks');
    return await response.json();
  } catch (error) {
    console.error('Error fetching looks:', error);
    // Fallback do mock data
    return [];
  }
}

export async function fetchLookById(id: string): Promise<any | null> {
  try {
    const response = await fetch(`${API_URL}/looks/${id}`);
    if (!response.ok) throw new Error('Failed to fetch look');
    return await response.json();
  } catch (error) {
    console.error('Error fetching look:', error);
    return null;
  }
}

export async function fetchUserById(id: string): Promise<any | null> {
  try {
    const response = await fetch(`${API_URL}/users/${id}`);
    if (!response.ok) throw new Error('Failed to fetch user');
    return await response.json();
  } catch (error) {
    console.error('Error fetching user:', error);
    return null;
  }
}

// Uwaga: W eksporcie statycznym API routes nie działają
// Musisz użyć zewnętrznego API backendu (np. na Railway, Render)
// lub przepisać aplikację na pełny client-side z mock data

