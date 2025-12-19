import { LatestLooksSection } from "./latest-looks-section";

export function LatestLooks() {
  return (
    <section className="py-16 md:py-24" id="najnowsze-sesje" aria-labelledby="najnowsze-sesje-heading">
      <div className="container mx-auto px-4">
        <h2 className="mb-12 text-center text-3xl font-bold md:text-4xl" id="najnowsze-sesje-heading">
          Najnowsze Sesje
        </h2>
        <LatestLooksSection />
      </div>
    </section>
  );
}

