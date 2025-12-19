import { Button } from "@/components/ui/button";
import Link from "next/link";

export function CTA() {
  return (
    <section className="py-16 md:py-24" id="zaczynaj" aria-labelledby="zaczynaj-heading">
      <div className="container mx-auto px-4">
        <div className="mx-auto max-w-2xl text-center">
          <h2 className="mb-6 text-3xl font-bold md:text-4xl" id="zaczynaj-heading">
            Zacznij już dziś!
          </h2>
          <p className="mb-8 text-lg text-muted-foreground">
            Dołącz do społeczności portal-modelingowy.pl i rozpocznij swoją przygodę z fotomodelingiem.
          </p>
          <Link href="/rejestracja">
            <Button size="lg" className="text-lg">
              Utwórz konto
            </Button>
          </Link>
        </div>
      </div>
    </section>
  );
}

