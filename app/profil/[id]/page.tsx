import { mockLooks } from "@/lib/mock-data";
import { LookCard } from "@/components/look-card";
import { notFound } from "next/navigation";

// Mock user data - in production, fetch from MySQL database
const mockUsers: Record<string, { name: string; bio?: string; location?: string; specialties?: string[] }> = {
  "photographer-1": {
    name: "Anna Kowalska",
    bio: "Fotografka specjalizująca się w portretach i sesjach modowych. Pracuję z naturalnym światłem i uwielbiam uchwycić autentyczne emocje.",
    location: "Warszawa",
    specialties: ["portret", "moda", "natura"],
  },
  "photographer-2": {
    name: "Piotr Zieliński",
    bio: "Fotograf produktowy i e-commerce. Specjalizuję się w fotografii biżuterii i małych przedmiotów.",
    location: "Kraków",
    specialties: ["e-commerce", "produkt", "studio"],
  },
  "photographer-3": {
    name: "Magdalena Lewandowska",
    bio: "Fotografka mody i stylizacji. Tworzę eleganckie sesje w studio i plenerze.",
    location: "Wrocław",
    specialties: ["moda", "studio", "stylizacja"],
  },
};

export async function generateStaticParams() {
  return Object.keys(mockUsers).map((id) => ({
    id,
  }));
}

export default function ProfilPage({ params }: { params: { id: string } }) {
  const user = mockUsers[params.id];

  if (!user) {
    notFound();
  }

  const userLooks = mockLooks.filter((look) => look.author_id === params.id);

  return (
    <main className="min-h-screen bg-white py-12 md:py-16">
      <div className="container mx-auto px-4">
        <div className="mx-auto max-w-6xl">
          <header className="mb-12">
            <h1 className="mb-4 text-4xl font-bold">{user.name}</h1>
            {user.location && (
              <p className="mb-4 text-lg text-muted-foreground">📍 {user.location}</p>
            )}
            {user.bio && (
              <p className="mb-4 text-gray-700 md:text-lg">{user.bio}</p>
            )}
            {user.specialties && user.specialties.length > 0 && (
              <div className="flex flex-wrap gap-2">
                {user.specialties.map((specialty) => (
                  <span
                    key={specialty}
                    className="rounded-full bg-primary/10 px-3 py-1 text-sm text-primary"
                  >
                    {specialty}
                  </span>
                ))}
              </div>
            )}
          </header>

          <section>
            <h2 className="mb-8 text-2xl font-semibold">Portfolio</h2>
            {userLooks.length > 0 ? (
              <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                {userLooks.map((look) => (
                  <LookCard key={look.id} look={look} />
                ))}
              </div>
            ) : (
              <p className="text-muted-foreground">Brak opublikowanych sesji.</p>
            )}
          </section>
        </div>
      </div>
    </main>
  );
}

