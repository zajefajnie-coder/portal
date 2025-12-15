import { ReactNode } from "react";

interface LegalPageLayoutProps {
  title: string;
  children: ReactNode;
}

export function LegalPageLayout({ title, children }: LegalPageLayoutProps) {
  return (
    <main className="min-h-screen bg-white py-12 md:py-16">
      <div className="container mx-auto px-4">
        <article className="mx-auto max-w-4xl">
          <header className="mb-8">
            <h1 className="text-3xl font-bold md:text-4xl">{title}</h1>
            <p className="mt-2 text-sm text-muted-foreground">
              Ostatnia aktualizacja: {new Date().toLocaleDateString("pl-PL", {
                year: "numeric",
                month: "long",
                day: "numeric",
              })}
            </p>
          </header>
          <div className="prose prose-gray max-w-none prose-headings:font-semibold prose-headings:text-gray-900 prose-p:text-gray-700 prose-ul:text-gray-700 prose-li:text-gray-700 prose-strong:text-gray-900">
            {children}
          </div>
        </article>
      </div>
    </main>
  );
}

