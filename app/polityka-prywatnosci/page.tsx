import { LegalPageLayout } from "@/components/legal-page-layout";

export const metadata = {
  title: "Polityka Prywatności | portal-modelingowy.pl",
  description: "Polityka prywatności platformy portal-modelingowy.pl zgodna z RODO",
};

export default function PolitykaPrywatnosciPage() {
  return (
    <LegalPageLayout title="Polityka Prywatności">
      <section className="mb-8">
        <h2 className="mb-4 text-2xl font-semibold">1. Informacje ogólne</h2>
        <p className="mb-4 text-gray-700">
          Niniejsza Polityka Prywatności określa zasady przetwarzania i ochrony danych
          osobowych użytkowników platformy{" "}
          <strong>portal-modelingowy.pl</strong> (zwanej dalej &ldquo;Platformą&rdquo;) zgodnie
          z Rozporządzeniem Parlamentu Europejskiego i Rady (UE) 2016/679 z dnia 27 kwietnia
          2016 r. w sprawie ochrony osób fizycznych w związku z przetwarzaniem danych
          osobowych i w sprawie swobodnego przepływu takich danych oraz uchylenia dyrektywy
          95/46/WE (ogólne rozporządzenie o ochronie danych) — RODO.
        </p>
        <p className="mb-4 text-gray-700">
          Administratorem danych osobowych jest{" "}
          <strong>[Nazwa firmy/osoby fizycznej prowadzącej działalność gospodarczą]</strong>,
          z siedzibą w{" "}
          <strong>[Adres]</strong>, NIP: <strong>[NIP]</strong>, REGON:{" "}
          <strong>[REGON]</strong>.
        </p>
        <p className="mb-4 text-gray-700">
          W sprawach dotyczących ochrony danych osobowych można kontaktować się z
          Administratorem pod adresem e-mail: <strong>[e-mail kontaktowy]</strong>.
        </p>
      </section>

      <section className="mb-8">
        <h2 className="mb-4 text-2xl font-semibold">2. Zakres przetwarzanych danych</h2>
        <p className="mb-4 text-gray-700">
          Administrator przetwarza następujące kategorie danych osobowych użytkowników:
        </p>
        <ul className="mb-4 ml-6 list-disc text-gray-700">
          <li>
            <strong>Dane identyfikacyjne:</strong> adres e-mail, imię i nazwisko lub nazwa
            użytkownika, opcjonalnie: zaimki (pronouns)
          </li>
          <li>
            <strong>Dane lokalizacyjne:</strong> opcjonalnie podana lokalizacja (miasto, region)
          </li>
          <li>
            <strong>Dane dotyczące doświadczenia:</strong> poziom doświadczenia w branży
            fotomodelingu (opcjonalnie)
          </li>
          <li>
            <strong>Dane biograficzne:</strong> biografia użytkownika, specjalizacje
            (opcjonalnie)
          </li>
          <li>
            <strong>Dane wizualne:</strong> zdjęcia publikowane w portfolio użytkownika
          </li>
          <li>
            <strong>Dane techniczne:</strong> adres IP, typ przeglądarki, dane z plików
            cookies, logi serwera
          </li>
          <li>
            <strong>Dane dotyczące współpracy:</strong> informacje o współpracownikach
            oznaczonych w sesjach, tagi sesji
          </li>
        </ul>
      </section>

      <section className="mb-8">
        <h2 className="mb-4 text-2xl font-semibold">3. Cel i podstawa prawna przetwarzania</h2>
        <p className="mb-4 text-gray-700">
          Dane osobowe przetwarzane są w następujących celach:
        </p>
        <ul className="mb-4 ml-6 list-disc text-gray-700">
          <li>
            <strong>Świadczenie usług Platformy</strong> — na podstawie art. 6 ust. 1 lit. b
            RODO (wykonanie umowy o świadczenie usług drogą elektroniczną)
          </li>
          <li>
            <strong>Rozwój i ulepszanie Platformy</strong> — na podstawie art. 6 ust. 1 lit. f
            RODO (prawnie uzasadniony interes administratora)
          </li>
          <li>
            <strong>Komunikacja z użytkownikami</strong> — na podstawie art. 6 ust. 1 lit. b
            RODO (wykonanie umowy) oraz art. 6 ust. 1 lit. f RODO (prawnie uzasadniony interes)
          </li>
          <li>
            <strong>Wypełnienie obowiązków prawnych</strong> — na podstawie art. 6 ust. 1 lit. c
            RODO (obowiązek prawny)
          </li>
          <li>
            <strong>Analiza statystyczna i marketingowa</strong> — na podstawie art. 6 ust. 1
            lit. a RODO (zgoda użytkownika) lub art. 6 ust. 1 lit. f RODO (prawnie uzasadniony
            interes)
          </li>
        </ul>
      </section>

      <section className="mb-8">
        <h2 className="mb-4 text-2xl font-semibold">4. Okres przechowywania danych</h2>
        <p className="mb-4 text-gray-700">
          Dane osobowe przechowywane są przez okres:
        </p>
        <ul className="mb-4 ml-6 list-disc text-gray-700">
          <li>
            Niezbędny do świadczenia usług Platformy — do momentu usunięcia konta przez
            użytkownika
          </li>
          <li>
            Wymagany przepisami prawa — zgodnie z obowiązującymi przepisami (np. przepisy
            podatkowe, przepisy o świadczeniu usług drogą elektroniczną)
          </li>
          <li>
            Do czasu wycofania zgody — w przypadku danych przetwarzanych na podstawie zgody
          </li>
        </ul>
        <p className="mb-4 text-gray-700">
          Po upływie okresu przechowywania, dane są nieodwracalnie usuwane lub anonimizowane.
        </p>
      </section>

      <section className="mb-8">
        <h2 className="mb-4 text-2xl font-semibold">5. Udostępnianie danych</h2>
        <p className="mb-4 text-gray-700">
          <strong>5.1.</strong> Dane osobowe mogą być udostępniane następującym kategoriom
          odbiorców:
        </p>
        <ul className="mb-4 ml-6 list-disc text-gray-700">
          <li>
            <strong>Dostawcy usług IT:</strong> Dostawca hostingu bazy danych MySQL (dane
            przechowywane w Unii Europejskiej lub zgodnie z umową o powierzeniu przetwarzania
            danych)
          </li>
          <li>
            <strong>Dostawcy usług analitycznych:</strong> w przypadku korzystania z narzędzi
            analitycznych (zgodnie z ustawieniami plików cookies)
          </li>
          <li>
            <strong>Organy państwowe:</strong> wyłącznie w przypadkach wymaganych przepisami prawa
          </li>
        </ul>
        <p className="mb-4 text-gray-700">
          <strong>5.2.</strong> Administrator <strong>nie sprzedaje</strong> danych osobowych
          użytkowników osobom trzecim.
        </p>
        <p className="mb-4 text-gray-700">
          <strong>5.3.</strong> Dane publikowane przez użytkownika w profilu publicznym
          (zdjęcia, biografia, nazwa) są widoczne dla wszystkich użytkowników Platformy oraz
          mogą być indeksowane przez wyszukiwarki internetowe.
        </p>
      </section>

      <section className="mb-8">
        <h2 className="mb-4 text-2xl font-semibold">6. Prawa użytkownika</h2>
        <p className="mb-4 text-gray-700">
          Użytkownik ma prawo do:
        </p>
        <ul className="mb-4 ml-6 list-disc text-gray-700">
          <li>
            <strong>Dostępu do danych</strong> — otrzymania informacji o przetwarzanych danych
            oraz kopii danych (art. 15 RODO)
          </li>
          <li>
            <strong>Sprostowania danych</strong> — poprawienia nieprawidłowych lub uzupełnienia
            niekompletnych danych (art. 16 RODO)
          </li>
          <li>
            <strong>Usunięcia danych</strong> — żądania usunięcia danych w przypadkach
            określonych w art. 17 RODO (&ldquo;prawo do bycia zapomnianym&rdquo;)
          </li>
          <li>
            <strong>Ograniczenia przetwarzania</strong> — w przypadkach określonych w art. 18
            RODO
          </li>
          <li>
            <strong>Przenoszenia danych</strong> — otrzymania danych w ustrukturyzowanym,
            powszechnie używanym formacie (art. 20 RODO)
          </li>
          <li>
            <strong>Sprzeciwu wobec przetwarzania</strong> — w przypadkach przetwarzania na
            podstawie prawnie uzasadnionego interesu (art. 21 RODO)
          </li>
          <li>
            <strong>Wycofania zgody</strong> — w każdej chwili, bez wpływu na zgodność z prawem
            przetwarzania przed wycofaniem zgody (art. 7 ust. 3 RODO)
          </li>
          <li>
            <strong>Wniesienia skargi do organu nadzorczego</strong> — Prezesa Urzędu Ochrony
            Danych Osobowych (art. 77 RODO)
          </li>
        </ul>
        <p className="mb-4 text-gray-700">
          Aby skorzystać z powyższych praw, należy skontaktować się z Administratorem pod
          adresem: <strong>[e-mail kontaktowy]</strong>.
        </p>
      </section>

      <section className="mb-8">
        <h2 className="mb-4 text-2xl font-semibold">7. Pliki cookies</h2>
        <p className="mb-4 text-gray-700">
          Platforma wykorzystuje pliki cookies w następujących celach:
        </p>
        <ul className="mb-4 ml-6 list-disc text-gray-700">
          <li>
            <strong>Niezbędne:</strong> zapewnienie podstawowej funkcjonalności Platformy
            (logowanie, sesja użytkownika)
          </li>
          <li>
            <strong>Funkcjonalne:</strong> zapamiętywanie preferencji użytkownika
          </li>
          <li>
            <strong>Analityczne:</strong> analiza ruchu na Platformie (za zgodą użytkownika)
          </li>
        </ul>
        <p className="mb-4 text-gray-700">
          Użytkownik może zarządzać preferencjami dotyczącymi plików cookies poprzez ustawienia
          przeglądarki lub banner zgody na pliki cookies wyświetlany na Platformie.
        </p>
      </section>

      <section className="mb-8">
        <h2 className="mb-4 text-2xl font-semibold">8. Bezpieczeństwo danych</h2>
        <p className="mb-4 text-gray-700">
          Administrator stosuje odpowiednie środki techniczne i organizacyjne zapewniające
          ochronę danych osobowych przed nieuprawnionym dostępem, utratą, zniszczeniem lub
          modyfikacją, w tym:
        </p>
        <ul className="mb-4 ml-6 list-disc text-gray-700">
          <li>Szyfrowanie połączeń (HTTPS/SSL)</li>
          <li>Bezpieczne przechowywanie haseł (hashowanie)</li>
          <li>Regularne aktualizacje oprogramowania</li>
          <li>Ograniczony dostęp do danych wyłącznie dla uprawnionych osób</li>
          <li>Regularne kopie zapasowe danych</li>
        </ul>
      </section>

      <section className="mb-8">
        <h2 className="mb-4 text-2xl font-semibold">9. Zmiany Polityki Prywatności</h2>
        <p className="mb-4 text-gray-700">
          Administrator zastrzega sobie prawo do wprowadzania zmian w Polityce Prywatności.
          O wszelkich zmianach użytkownicy zostaną poinformowani poprzez komunikat na
          Platformie lub wiadomość e-mail. Aktualna wersja Polityki Prywatności jest zawsze
          dostępna na Platformie.
        </p>
      </section>

      <section className="mb-8">
        <h2 className="mb-4 text-2xl font-semibold">10. Kontakt</h2>
        <p className="mb-4 text-gray-700">
          W sprawach dotyczących ochrony danych osobowych oraz realizacji praw użytkownika,
          prosimy o kontakt:
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

