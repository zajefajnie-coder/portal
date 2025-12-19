import Link from "next/link";

export function Footer() {
  const currentYear = new Date().getFullYear();

  return (
    <footer className="border-t bg-gray-50 py-8" role="contentinfo">
      <div className="container mx-auto px-4">
        <div className="flex flex-col items-center justify-between gap-4 md:flex-row">
          <div className="text-sm text-muted-foreground">
            © {currentYear} portal-modelingowy.pl — Wszelkie prawa zastrzeżone.
          </div>
          <nav aria-label="Stopka nawigacja">
            <ul className="flex flex-wrap items-center justify-center gap-4 text-sm">
              <li>
                <Link
                  href="/regulamin"
                  className="text-muted-foreground transition-colors hover:text-foreground"
                >
                  Regulamin
                </Link>
              </li>
              <li>
                <Link
                  href="/polityka-prywatnosci"
                  className="text-muted-foreground transition-colors hover:text-foreground"
                >
                  Polityka Prywatności
                </Link>
              </li>
              <li>
                <Link
                  href="/rodo"
                  className="text-muted-foreground transition-colors hover:text-foreground"
                >
                  RODO
                </Link>
              </li>
              <li>
                <Link
                  href="/kontakt"
                  className="text-muted-foreground transition-colors hover:text-foreground"
                >
                  Kontakt
                </Link>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </footer>
  );
}

