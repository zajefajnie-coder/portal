/** @type {import('next').NextConfig} */
const nextConfig = {
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
  },
};

export default nextConfig;

