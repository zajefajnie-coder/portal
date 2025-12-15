import { mockLooks } from "@/lib/mock-data";
import Image from "next/image";
import { notFound } from "next/navigation";
import Link from "next/link";

export async function generateStaticParams() {
  return mockLooks.map((look) => ({
    id: look.id,
  }));
}

export default function LookPage({ params }: { params: { id: string } }) {
  const look = mockLooks.find((l) => l.id === params.id);

  if (!look) {
    notFound();
  }

  return (
    <main className="min-h-screen bg-white py-12 md:py-16">
      <div className="container mx-auto px-4">
        <article className="mx-auto max-w-4xl">
          <header className="mb-8">
            <h1 className="mb-4 text-3xl font-bold md:text-4xl">{look.title}</h1>
            <div className="flex flex-wrap gap-4 text-sm text-muted-foreground">
              {look.location && <span>📍 {look.location}</span>}
              <span>📅 {new Date(look.date).toLocaleDateString("pl-PL")}</span>
              <span>
                Autor:{" "}
                <Link href={`/profil/${look.author_id}`} className="text-primary underline">
                  {look.author_name}
                </Link>
              </span>
            </div>
          </header>

          <div className="mb-8">
            <div className="relative aspect-[3/4] w-full overflow-hidden rounded-lg bg-gray-100">
              <Image
                src={look.image_url}
                alt={look.image_alt}
                fill
                className="object-cover"
                priority
                sizes="(max-width: 768px) 100vw, 800px"
              />
            </div>
          </div>

          {look.collaborators && look.collaborators.length > 0 && (
            <section className="mb-8">
              <h2 className="mb-4 text-2xl font-semibold">Współpracownicy</h2>
              <ul className="list-disc pl-6">
                {look.collaborators.map((collab, idx) => (
                  <li key={idx} className="text-gray-700">
                    {collab}
                  </li>
                ))}
              </ul>
            </section>
          )}

          <section className="mb-8">
            <h2 className="mb-4 text-2xl font-semibold">Tagi</h2>
            <div className="flex flex-wrap gap-2">
              {look.tags.map((tag) => (
                <span
                  key={tag}
                  className="rounded-full bg-secondary px-3 py-1 text-sm text-secondary-foreground"
                >
                  #{tag}
                </span>
              ))}
            </div>
          </section>
        </article>
      </div>
    </main>
  );
}

