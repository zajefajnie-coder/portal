import { LegalPageLayout } from "@/components/legal-page-layout";
import Link from "next/link";

export const metadata = {
  title: "Informacja RODO | portal-modelingowy.pl",
  description: "Informacja o przetwarzaniu danych osobowych zgodnie z RODO",
};

export default function RodPage() {
  return (
    <LegalPageLayout title="Informacja RODO">
      <section className="mb-8">
        <h2 className="mb-4 text-2xl font-semibold">Administrator danych osobowych</h2>
        <p className="mb-4 text-gray-700">
          Administratorem danych osobowych przetwarzanych w ramach platformy{" "}
          <strong>portal-modelingowy.pl</strong> jest:
        </p>
        <div className="mb-4 rounded-lg bg-gray-50 p-4">
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
            E-mail kontaktowy: <strong>[e-mail kontaktowy]</strong>
          </p>
        </div>
      </section>

      <section className="mb-8">
        <h2 className="mb-4 text-2xl font-semibold">Inspektor Ochrony Danych</h2>
        <p className="mb-4 text-gray-700">
          {process.env.NEXT_PUBLIC_HAS_IOD === "true" ? (
            <>
              Administrator wyznaczył Inspektora Ochrony Danych (IOD), z którym można
              skontaktować się w sprawach dotyczących przetwarzania danych osobowych:
              <br />
              <strong>[Imię i nazwisko IOD]</strong>
              <br />
              E-mail: <strong>[e-mail IOD]</strong>
            </>
          ) : (
            <>
              Administrator nie wyznaczył Inspektora Ochrony Danych, gdyż nie jest to wymagane
              przepisami RODO. W sprawach dotyczących przetwarzania danych osobowych prosimy
              o kontakt bezpośrednio z Administratorem.
            </>
          )}
        </p>
      </section>

      <section className="mb-8">
        <h2 className="mb-4 text-2xl font-semibold">Podstawa prawna i cel przetwarzania</h2>
        <p className="mb-4 text-gray-700">
          Dane osobowe przetwarzane są w celu świadczenia usług platformy{" "}
          <strong>portal-modelingowy.pl</strong> na podstawie:
        </p>
        <ul className="mb-4 ml-6 list-disc text-gray-700">
          <li>
            Art. 6 ust. 1 lit. b RODO — wykonanie umowy o świadczenie usług drogą
            elektroniczną
          </li>
          <li>
            Art. 6 ust. 1 lit. f RODO — prawnie uzasadniony interes administratora (rozwój
            platformy, zapewnienie bezpieczeństwa)
          </li>
          <li>
            Art. 6 ust. 1 lit. a RODO — zgoda użytkownika (w przypadku danych opcjonalnych
            oraz plików cookies analitycznych)
          </li>
        </ul>
      </section>

      <section className="mb-8">
        <h2 className="mb-4 text-2xl font-semibold">Prawa użytkownika</h2>
        <p className="mb-4 text-gray-700">
          Użytkownik ma prawo do:
        </p>
        <ul className="mb-4 ml-6 list-disc text-gray-700">
          <li>Dostępu do swoich danych osobowych</li>
          <li>Sprostowania danych</li>
          <li>Usunięcia danych („prawo do bycia zapomnianym")</li>
          <li>Ograniczenia przetwarzania</li>
          <li>Przenoszenia danych</li>
          <li>Sprzeciwu wobec przetwarzania</li>
          <li>Wycofania zgody w dowolnym momencie</li>
          <li>
            Wniesienia skargi do Prezesa Urzędu Ochrony Danych Osobowych (ul. Stawki 2, 00-193
            Warszawa)
          </li>
        </ul>
        <p className="mb-4 text-gray-700">
          Aby skorzystać z powyższych praw, należy skontaktować się z Administratorem pod
          adresem: <strong>[e-mail kontaktowy]</strong>.
        </p>
      </section>

      <section className="mb-8">
        <h2 className="mb-4 text-2xl font-semibold">Okres przechowywania danych</h2>
        <p className="mb-4 text-gray-700">
          Dane osobowe przechowywane są przez okres niezbędny do świadczenia usług Platformy,
          a następnie przez okres wymagany przepisami prawa (np. przepisy podatkowe) lub do
          momentu wycofania zgody przez użytkownika.
        </p>
      </section>

      <section className="mb-8">
        <h2 className="mb-4 text-2xl font-semibold">Udostępnianie danych</h2>
        <p className="mb-4 text-gray-700">
          Dane osobowe mogą być udostępniane dostawcom usług IT (hosting bazy danych MySQL —
          dane przechowywane w Unii Europejskiej lub zgodnie z umową o powierzeniu przetwarzania
          danych) oraz organom państwowym wyłącznie w przypadkach wymaganych przepisami prawa.
          Administrator nie sprzedaje danych osobowych osobom trzecim.
        </p>
      </section>

      <section className="mb-8">
        <h2 className="mb-4 text-2xl font-semibold">Szczegółowe informacje</h2>
        <p className="mb-4 text-gray-700">
          Szczegółowe informacje dotyczące przetwarzania danych osobowych znajdują się w:
        </p>
        <ul className="mb-4 ml-6 list-disc text-gray-700">
          <li>
            <Link href="/polityka-prywatnosci" className="text-primary underline">
              Polityce Prywatności
            </Link>
          </li>
          <li>
            <Link href="/regulamin" className="text-primary underline">
              Regulaminie
            </Link>
          </li>
        </ul>
      </section>

      <section className="mb-8">
        <h2 className="mb-4 text-2xl font-semibold">Kontakt</h2>
        <p className="mb-4 text-gray-700">
          W sprawach dotyczących ochrony danych osobowych prosimy o kontakt:
        </p>
        <p className="mb-4 text-gray-700">
          E-mail: <strong>[e-mail kontaktowy]</strong>
          <br />
          Adres: <strong>[Adres]</strong>
        </p>
      </section>
    </LegalPageLayout>
  );
}

