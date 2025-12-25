import type { Metadata } from "next";
import { Inter } from "next/font/google";
import "./globals.css";
import { Header } from "@/components/header";
import { Footer } from "@/components/footer";
import { CookieConsent } from "@/components/cookie-consent";
import { SpeedInsights } from "@vercel/speed-insights/next";

const inter = Inter({ subsets: ["latin"] });

export const metadata: Metadata = {
  title: "portal-modelingowy.pl | Platforma dla społeczności fotomodelingu",
  description: "Przeglądaj najświeższe sesje społeczności portal-modelingowy.pl. Współtwórz z osobami z branży fotomodelingu. Niezależnie czy dopiero zaczynasz, czy masz lata doświadczenia — masz tu swoje miejsce.",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="pl">
      <body className={inter.className}>
        <Header />
        {children}
        <Footer />
        <CookieConsent />
        <SpeedInsights />
      </body>
    </html>
  );
}

