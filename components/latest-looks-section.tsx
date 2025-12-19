import { mockLooks } from "@/lib/mock-data";
import { LookCard } from "./look-card";

export function LatestLooksSection() {
  return (
    <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
      {mockLooks.map((look) => (
        <LookCard key={look.id} look={look} />
      ))}
    </div>
  );
}

