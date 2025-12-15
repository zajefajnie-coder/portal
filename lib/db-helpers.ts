import { query, queryOne } from './db';
import { Look, User } from './db';

// Helper functions for common database operations

export async function getLookById(id: string): Promise<Look | null> {
  const result = await queryOne<{
    id: string;
    title: string;
    date: string;
    location: string | null;
    image_url: string;
    image_alt: string;
    author_id: string;
    author_name: string;
    tags: string;
  }>(
    `SELECT 
      l.id,
      l.title,
      l.date,
      l.location,
      l.image_url,
      l.image_alt,
      l.author_id,
      u.name as author_name,
      l.tags
    FROM looks l
    JOIN users u ON l.author_id = u.id
    WHERE l.id = ? AND l.is_public = TRUE`,
    [id]
  );

  if (!result) return null;

  // Pobierz współpracowników osobno
  const collaborators = await query<{
    name: string;
    role: string;
  }>(
    `SELECT name, role
    FROM collaborators
    WHERE look_id = ?`,
    [id]
  );

  return {
    id: result.id,
    title: result.title,
    date: result.date,
    location: result.location || undefined,
    image_url: result.image_url,
    image_alt: result.image_alt,
    author_id: result.author_id,
    author_name: result.author_name,
    tags: JSON.parse(result.tags || '[]'),
    collaborators: collaborators.length > 0 
      ? collaborators.map(c => `${c.name} (${c.role})`)
      : undefined,
  };
}

export async function getLatestLooks(limit: number = 10): Promise<Look[]> {
  const results = await query<{
    id: string;
    title: string;
    date: string;
    location: string | null;
    image_url: string;
    image_alt: string;
    author_id: string;
    author_name: string;
    tags: string;
  }>(
    `SELECT 
      l.id,
      l.title,
      l.date,
      l.location,
      l.image_url,
      l.image_alt,
      l.author_id,
      u.name as author_name,
      l.tags
    FROM looks l
    JOIN users u ON l.author_id = u.id
    WHERE l.is_public = TRUE
    ORDER BY l.created_at DESC
    LIMIT ?`,
    [limit]
  );

  return results.map((row) => ({
    id: row.id,
    title: row.title,
    date: row.date,
    location: row.location || undefined,
    image_url: row.image_url,
    image_alt: row.image_alt,
    author_id: row.author_id,
    author_name: row.author_name,
    tags: JSON.parse(row.tags || '[]'),
  }));
}

export async function getUserById(id: string): Promise<User | null> {
  const result = await queryOne<{
    id: string;
    name: string;
    pronouns: string | null;
    location: string | null;
    experience_level: string | null;
    bio: string | null;
    specialties: string;
  }>(
    `SELECT 
      id,
      name,
      pronouns,
      location,
      experience_level,
      bio,
      specialties
    FROM users
    WHERE id = ?`,
    [id]
  );

  if (!result) return null;

  return {
    id: result.id,
    name: result.name,
    pronouns: result.pronouns || undefined,
    location: result.location || undefined,
    experience_level: result.experience_level || undefined,
    bio: result.bio || undefined,
    specialties: JSON.parse(result.specialties || '[]'),
  };
}

export async function getUserLooks(userId: string): Promise<Look[]> {
  const results = await query<{
    id: string;
    title: string;
    date: string;
    location: string | null;
    image_url: string;
    image_alt: string;
    author_id: string;
    author_name: string;
    tags: string;
  }>(
    `SELECT 
      l.id,
      l.title,
      l.date,
      l.location,
      l.image_url,
      l.image_alt,
      l.author_id,
      u.name as author_name,
      l.tags
    FROM looks l
    JOIN users u ON l.author_id = u.id
    WHERE l.author_id = ? AND l.is_public = TRUE
    ORDER BY l.created_at DESC`,
    [userId]
  );

  return results.map((row) => ({
    id: row.id,
    title: row.title,
    date: row.date,
    location: row.location || undefined,
    image_url: row.image_url,
    image_alt: row.image_alt,
    author_id: row.author_id,
    author_name: row.author_name,
    tags: JSON.parse(row.tags || '[]'),
  }));
}

