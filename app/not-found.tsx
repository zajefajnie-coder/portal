import Link from "next/link";
import { Button } from "@/components/ui/button";

export default function NotFound() {
  return (
    <main className="flex min-h-screen flex-col items-center justify-center bg-white px-4">
      <div className="mx-auto max-w-md text-center">
        <h1 className="mb-4 text-6xl font-bold">404</h1>
        <h2 className="mb-4 text-2xl font-semibold">Strona nie została znaleziona</h2>
        <p className="mb-8 text-muted-foreground">
          Przepraszamy, strona której szukasz nie istnieje lub została przeniesiona.
        </p>
        <Link href="/">
          <Button>Wróć do strony głównej</Button>
        </Link>
      </div>
    </main>
  );
}

