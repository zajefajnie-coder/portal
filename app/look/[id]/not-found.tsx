import Link from "next/link";
import { Button } from "@/components/ui/button";

export default function LookNotFound() {
  return (
    <main className="flex min-h-screen flex-col items-center justify-center bg-white px-4">
      <div className="mx-auto max-w-md text-center">
        <h1 className="mb-4 text-4xl font-bold">Sesja nie została znaleziona</h1>
        <p className="mb-8 text-muted-foreground">
          Przepraszamy, sesja której szukasz nie istnieje lub została usunięta.
        </p>
        <Link href="/">
          <Button>Wróć do strony głównej</Button>
        </Link>
      </div>
    </main>
  );
}

