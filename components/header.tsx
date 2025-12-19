"use client";

import Link from "next/link";
import { Button } from "@/components/ui/button";
import { useState } from "react";
import { Menu, X } from "lucide-react";

export function Header() {
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

  return (
    <header className="sticky top-0 z-40 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
      <div className="container mx-auto flex h-16 items-center justify-between px-4">
        <Link href="/" className="text-xl font-bold">
          portal-modelingowy.pl
        </Link>
        <nav className="hidden items-center gap-6 md:flex" aria-label="Główne menu">
          <Link
            href="/#najnowsze-sesje"
            className="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"
          >
            Najnowsze Sesje
          </Link>
          <Link
            href="/#dla-tworcow"
            className="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"
          >
            Dla Twórców
          </Link>
          <Link
            href="/#zaczynaj"
            className="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"
          >
            Zacznij
          </Link>
        </nav>
        <div className="hidden items-center gap-4 md:flex">
          <Link href="/rejestracja">
            <Button variant="outline" size="sm">
              Zarejestruj się
            </Button>
          </Link>
          <Link href="/logowanie">
            <Button size="sm">Zaloguj się</Button>
          </Link>
        </div>
        <button
          className="md:hidden"
          onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
          aria-label="Otwórz menu"
          aria-expanded={mobileMenuOpen}
        >
          {mobileMenuOpen ? <X className="h-6 w-6" /> : <Menu className="h-6 w-6" />}
        </button>
      </div>
      {mobileMenuOpen && (
        <nav
          className="border-t bg-background md:hidden"
          aria-label="Menu mobilne"
        >
          <div className="container mx-auto flex flex-col gap-4 px-4 py-4">
            <Link
              href="/#najnowsze-sesje"
              className="text-sm font-medium"
              onClick={() => setMobileMenuOpen(false)}
            >
              Najnowsze Sesje
            </Link>
            <Link
              href="/#dla-tworcow"
              className="text-sm font-medium"
              onClick={() => setMobileMenuOpen(false)}
            >
              Dla Twórców
            </Link>
            <Link
              href="/#zaczynaj"
              className="text-sm font-medium"
              onClick={() => setMobileMenuOpen(false)}
            >
              Zacznij
            </Link>
            <div className="flex flex-col gap-2 pt-2">
              <Link href="/rejestracja" onClick={() => setMobileMenuOpen(false)}>
                <Button variant="outline" size="sm" className="w-full">
                  Zarejestruj się
                </Button>
              </Link>
              <Link href="/logowanie" onClick={() => setMobileMenuOpen(false)}>
                <Button size="sm" className="w-full">
                  Zaloguj się
                </Button>
              </Link>
            </div>
          </div>
        </nav>
      )}
    </header>
  );
}

