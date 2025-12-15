import mysql from 'mysql2/promise';

// Types for our database
export interface Look {
  id: string;
  title: string;
  date: string;
  location?: string;
  image_url: string;
  image_alt: string;
  author_id: string;
  author_name: string;
  tags: string[];
  collaborators?: string[];
}

export interface User {
  id: string;
  name: string;
  pronouns?: string;
  location?: string;
  experience_level?: string;
  bio?: string;
  specialties?: string[];
}

// Database connection pool
let pool: mysql.Pool | null = null;

export function getPool(): mysql.Pool {
  if (!pool) {
    // Obsługa connection string (dla PlanetScale, Railway, itp.)
    const databaseUrl = process.env.DATABASE_URL;
    
    if (databaseUrl) {
      // Parsuj connection string (format: mysql://user:password@host:port/database)
      try {
        // Usuń prefix mysql:// i dodaj https:// dla parsowania URL
        const urlString = databaseUrl.replace(/^mysql:\/\//, 'https://');
        const url = new URL(urlString);
        
        pool = mysql.createPool({
          host: url.hostname,
          port: parseInt(url.port || '3306'),
          user: decodeURIComponent(url.username),
          password: decodeURIComponent(url.password),
          database: url.pathname.slice(1).split('?')[0],
          waitForConnections: true,
          connectionLimit: 10,
          queueLimit: 0,
          enableKeepAlive: true,
          keepAliveInitialDelay: 0,
          ssl: {
            rejectUnauthorized: false, // Dla PlanetScale i innych cloud providers
          },
        });
      } catch (error) {
        console.error('Error parsing DATABASE_URL:', error);
        // Fallback do standardowej konfiguracji
        pool = createStandardPool();
      }
    } else {
      // Standardowa konfiguracja przez zmienne środowiskowe
      pool = createStandardPool();
    }
  }
  return pool;
}

function createStandardPool(): mysql.Pool {
  return mysql.createPool({
    host: process.env.DB_HOST || 'localhost',
    port: parseInt(process.env.DB_PORT || '3306'),
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASSWORD || '',
    database: process.env.DB_NAME || 'portal_modelingowy',
    waitForConnections: true,
    connectionLimit: 10,
    queueLimit: 0,
    enableKeepAlive: true,
    keepAliveInitialDelay: 0,
    // SSL dla produkcji (jeśli wymagane)
    ssl: process.env.DB_SSL === 'true' ? {
      rejectUnauthorized: false,
    } : undefined,
  });
}

// Helper function to execute queries
export async function query<T = any>(
  sql: string,
  params?: any[]
): Promise<T[]> {
  const connection = getPool();
  const [rows] = await connection.execute(sql, params);
  return rows as T[];
}

// Helper function to execute a single query
export async function queryOne<T = any>(
  sql: string,
  params?: any[]
): Promise<T | null> {
  const results = await query<T>(sql, params);
  return results.length > 0 ? results[0] : null;
}

// Close pool (useful for cleanup in tests)
export async function closePool(): Promise<void> {
  if (pool) {
    await pool.end();
    pool = null;
  }
}

