import { Hero } from "@/components/hero";
import { LatestLooks } from "@/components/latest-looks";
import { ForCreators } from "@/components/for-creators";
import { CTA } from "@/components/cta";

export default function HomePage() {
  return (
    <main>
      <Hero />
      <LatestLooks />
      <ForCreators />
      <CTA />
    </main>
  );
}

