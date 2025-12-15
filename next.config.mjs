/** @type {import('next').NextConfig} */
const isStaticExport = process.env.NEXT_PUBLIC_STATIC_EXPORT === 'true';

const nextConfig = {
  // Eksport statyczny dla Zenbox (hosting PHP/MySQL)
  // Automatycznie włączany gdy NEXT_PUBLIC_STATIC_EXPORT=true
  ...(isStaticExport && { output: 'export' }),
  
  images: {
    remotePatterns: [
      {
        protocol: 'https',
        hostname: '**.unsplash.com',
      },
      {
        protocol: 'https',
        hostname: '**.cloudinary.com',
      },
      // Dodaj domeny gdzie będziesz hostować zdjęcia
    ],
    // Dla eksportu statycznego wyłącz optymalizację obrazów
    unoptimized: isStaticExport,
  },
  // Wyłącz trailing slash dla lepszej kompatybilności
  trailingSlash: false,
};

export default nextConfig;

