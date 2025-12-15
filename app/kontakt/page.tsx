import { LegalPageLayout } from "@/components/legal-page-layout";

export const metadata = {
  title: "Kontakt | portal-modelingowy.pl",
  description: "Skontaktuj się z nami",
};

export default function KontaktPage() {
  return (
    <LegalPageLayout title="Kontakt">
      <section className="mb-8">
        <h2 className="mb-4 text-2xl font-semibold">Dane kontaktowe</h2>
        <div className="mb-4 rounded-lg bg-gray-50 p-6">
          <p className="mb-2 text-gray-700">
            <strong>[Nazwa firmy/osoby fizycznej prowadzącej działalność gospodarczą]</strong>
          </p>
          <p className="mb-2 text-gray-700">
            Adres: <strong>[Adres]</strong>
          </p>
          <p className="mb-2 text-gray-700">
            NIP: <strong>[NIP]</strong>
          </p>
          <p className="mb-2 text-gray-700">
            REGON: <strong>[REGON]</strong>
          </p>
          <p className="text-gray-700">
            E-mail: <strong>[e-mail kontaktowy]</strong>
          </p>
        </div>
      </section>

      <section className="mb-8">
        <h2 className="mb-4 text-2xl font-semibold">Formularz kontaktowy</h2>
        <p className="mb-4 text-gray-700">
          W sprawach dotyczących Platformy, pytań technicznych lub współpracy, prosimy o
          kontakt pod adresem e-mail: <strong>[e-mail kontaktowy]</strong>.
        </p>
        <p className="mb-4 text-gray-700">
          Odpowiadamy na wiadomości w ciągu 48 godzin roboczych.
        </p>
      </section>

      <section className="mb-8">
        <h2 className="mb-4 text-2xl font-semibold">Wsparcie techniczne</h2>
        <p className="mb-4 text-gray-700">
          W przypadku problemów technicznych lub pytań dotyczących funkcjonowania Platformy,
          prosimy o kontakt pod adresem: <strong>[e-mail wsparcia]</strong>.
        </p>
      </section>
    </LegalPageLayout>
  );
}

