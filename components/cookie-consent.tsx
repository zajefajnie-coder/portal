"use client";

import { useState, useEffect } from "react";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import Link from "next/link";

export function CookieConsent() {
  const [isVisible, setIsVisible] = useState(false);

  useEffect(() => {
    const consent = localStorage.getItem("cookie-consent");
    if (!consent) {
      setIsVisible(true);
    }
  }, []);

  const handleAccept = () => {
    localStorage.setItem("cookie-consent", "accepted");
    setIsVisible(false);
  };

  const handleReject = () => {
    localStorage.setItem("cookie-consent", "rejected");
    setIsVisible(false);
  };

  if (!isVisible) return null;

  return (
    <div
      className="fixed bottom-0 left-0 right-0 z-50 p-4 md:p-6"
      role="dialog"
      aria-labelledby="cookie-consent-heading"
      aria-modal="true"
    >
      <Card className="mx-auto max-w-4xl p-6 shadow-lg">
        <div className="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
          <div className="flex-1">
            <h3 id="cookie-consent-heading" className="mb-2 text-lg font-semibold">
              Pliki cookie
            </h3>
            <p className="text-sm text-muted-foreground">
              Ta strona używa plików cookie, aby zapewnić najlepsze doświadczenie.
              Niezbędne pliki cookie są zawsze aktywne. Możesz zarządzać preferencjami w{" "}
              <Link href="/polityka-prywatnosci" className="underline">
                Polityce Prywatności
              </Link>
              .
            </p>
          </div>
          <div className="flex gap-2">
            <Button variant="outline" onClick={handleReject} size="sm">
              Odrzuć
            </Button>
            <Button onClick={handleAccept} size="sm">
              Akceptuję
            </Button>
          </div>
        </div>
      </Card>
    </div>
  );
}

