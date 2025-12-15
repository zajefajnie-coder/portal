import { LegalPageLayout } from "@/components/legal-page-layout";

export const metadata = {
  title: "Regulamin | portal-modelingowy.pl",
  description: "Regulamin korzystania z platformy portal-modelingowy.pl",
};

export default function RegulaminPage() {
  return (
    <LegalPageLayout title="Regulamin">
      <section className="mb-8">
        <h2 className="mb-4 text-2xl font-semibold">1. Postanowienia ogólne</h2>
        <p className="mb-4 text-gray-700">
          Niniejszy Regulamin określa zasady korzystania z platformy internetowej{" "}
          <strong>portal-modelingowy.pl</strong> (zwanej dalej &ldquo;Platformą&rdquo;), będącej
          serwisem społecznościowym dedykowanym osobom związanym z branżą fotomodelingu
          w Polsce.
        </p>
        <p className="mb-4 text-gray-700">
          Administratorem Platformy jest{" "}
          <strong>[Nazwa firmy/osoby fizycznej prowadzącej działalność gospodarczą]</strong>,
          z siedzibą w{" "}
          <strong>[Adres]</strong>, NIP: <strong>[NIP]</strong>, REGON:{" "}
          <strong>[REGON]</strong>.
        </p>
        <p className="mb-4 text-gray-700">
          Korzystanie z Platformy jest równoznaczne z akceptacją niniejszego Regulaminu.
          W przypadku braku akceptacji Regulaminu, użytkownik zobowiązany jest do
          niekorzystania z Platformy.
        </p>
      </section>

      <section className="mb-8">
        <h2 className="mb-4 text-2xl font-semibold">2. Zakres usługi</h2>
        <p className="mb-4 text-gray-700">
          Platforma umożliwia użytkownikom:
        </p>
        <ul className="mb-4 ml-6 list-disc text-gray-700">
          <li>Tworzenie i zarządzanie profilami użytkowników</li>
          <li>Publikowanie portfolio w formie &ldquo;Looks&rdquo; (sesji fotograficznych)</li>
          <li>Przeglądanie portfolio innych użytkowników</li>
          <li>Nawiązywanie kontaktów z innymi członkami społeczności</li>
          <li>Oznaczanie współpracowników w publikowanych sesjach</li>
          <li>Dodawanie tagów do sesji w celu kategoryzacji</li>
        </ul>
      </section>

      <section className="mb-8">
        <h2 className="mb-4 text-2xl font-semibold">3. Konto użytkownika</h2>
        <p className="mb-4 text-gray-700">
          Aby korzystać z pełnej funkcjonalności Platformy, użytkownik zobowiązany jest
          do utworzenia konta poprzez podanie adresu e-mail oraz hasła, lub poprzez
          logowanie za pomocą konta Google.
        </p>
        <p className="mb-4 text-gray-700">
          Użytkownik zobowiązany jest do:
        </p>
        <ul className="mb-4 ml-6 list-disc text-gray-700">
          <li>Podawania prawdziwych i aktualnych danych</li>
          <li>Zachowania poufności danych logowania</li>
          <li>Nieudostępniania konta osobom trzecim</li>
          <li>Niezwłocznego powiadomienia Administratora o nieuprawnionym dostępie do konta</li>
        </ul>
      </section>

      <section className="mb-8">
        <h2 className="mb-4 text-2xl font-semibold">4. Prawa autorskie</h2>
        <p className="mb-4 text-gray-700">
          <strong>4.1.</strong> Wszystkie prawa autorskie do treści publikowanych na Platformie
          (w tym zdjęć, tekstów, grafik) przysługują ich twórcom (fotografom, modelom,
          członkom zespołu kreatywnego) zgodnie z przepisami ustawy z dnia 4 lutego 1994 r.
          o prawie autorskim i prawach pokrewnych.
        </p>
        <p className="mb-4 text-gray-700">
          <strong>4.2.</strong> Publikując treści na Platformie, użytkownik udziela
          Administratorowi Platformy licencji nie wyłącznej, bezpłatnej, niewyłącznej,
          na czas nieoznaczony, na terytorium całego świata, do:
        </p>
        <ul className="mb-4 ml-6 list-disc text-gray-700">
          <li>Prezentacji treści w ramach funkcjonowania Platformy</li>
          <li>Wyświetlania treści w portfolio użytkownika</li>
          <li>Użycia treści w celach promocyjnych Platformy (z zachowaniem oznaczenia autorstwa)</li>
        </ul>
        <p className="mb-4 text-gray-700">
          <strong>4.3.</strong> Użytkownik zachowuje pełne prawa autorskie do swoich treści
          i może w każdej chwili usunąć je z Platformy.
        </p>
        <p className="mb-4 text-gray-700">
          <strong>4.4.</strong> Użytkownik oświadcza, że posiada wszystkie niezbędne prawa
          do publikowanych treści, w tym zgody osób na nich widocznych oraz prawa do
          wykorzystania utworów osób trzecich.
        </p>
      </section>

      <section className="mb-8">
        <h2 className="mb-4 text-2xl font-semibold">5. Zabronione treści i zachowania</h2>
        <p className="mb-4 text-gray-700">
          Zabronione jest publikowanie treści:
        </p>
        <ul className="mb-4 ml-6 list-disc text-gray-700">
          <li>Pornograficznych lub o charakterze erotycznym</li>
          <li>Rasistowskich, dyskryminujących lub nawołujących do nienawiści</li>
          <li>Naruszających prawa osób trzecich (w tym prawa autorskie, prawa do wizerunku)</li>
          <li>Zawierających wizerunki osób bez ich zgody</li>
          <li>Oszczerczych, zniesławiających lub naruszających dobre imię</li>
          <li>Reklamowych lub spamerskich</li>
          <li>Zawierających dane osobowe osób trzecich bez ich zgody</li>
        </ul>
        <p className="mb-4 text-gray-700">
          Zabronione jest również:
        </p>
        <ul className="mb-4 ml-6 list-disc text-gray-700">
          <li>Wykorzystywanie Platformy w celach niezgodnych z jej przeznaczeniem</li>
          <li>Próby włamania lub naruszenia bezpieczeństwa Platformy</li>
          <li>Podszywanie się pod inne osoby</li>
          <li>Nadużywanie funkcji Platformy w sposób zakłócający jej działanie</li>
        </ul>
      </section>

      <section className="mb-8">
        <h2 className="mb-4 text-2xl font-semibold">6. Moderacja i usunięcie konta</h2>
        <p className="mb-4 text-gray-700">
          <strong>6.1.</strong> Administrator zastrzega sobie prawo do moderacji treści
          publikowanych na Platformie oraz usuwania treści naruszających Regulamin lub
          przepisy prawa.
        </p>
        <p className="mb-4 text-gray-700">
          <strong>6.2.</strong> W przypadku naruszenia Regulaminu, Administrator może:
        </p>
        <ul className="mb-4 ml-6 list-disc text-gray-700">
          <li>Usunąć naruszające treści</li>
          <li>Zablokować dostęp do konta czasowo lub na stałe</li>
          <li>Usunąć konto użytkownika</li>
        </ul>
        <p className="mb-4 text-gray-700">
          <strong>6.3.</strong> Użytkownik może w każdej chwili usunąć swoje konto poprzez
          odpowiednią funkcję w ustawieniach konta lub kontaktując się z Administratorem.
        </p>
      </section>

      <section className="mb-8">
        <h2 className="mb-4 text-2xl font-semibold">7. Odpowiedzialność</h2>
        <p className="mb-4 text-gray-700">
          <strong>7.1.</strong> Administrator nie ponosi odpowiedzialności za treści
          publikowane przez użytkowników oraz za ewentualne szkody wynikające z ich
          publikacji.
        </p>
        <p className="mb-4 text-gray-700">
          <strong>7.2.</strong> Użytkownik ponosi pełną odpowiedzialność za treści
          publikowane przez siebie na Platformie.
        </p>
        <p className="mb-4 text-gray-700">
          <strong>7.3.</strong> Administrator nie gwarantuje nieprzerwanego działania
          Platformy oraz zastrzega sobie prawo do czasowego zawieszenia jej działania
          w celach technicznych.
        </p>
      </section>

      <section className="mb-8">
        <h2 className="mb-4 text-2xl font-semibold">8. Zmiany Regulaminu</h2>
        <p className="mb-4 text-gray-700">
          Administrator zastrzega sobie prawo do wprowadzania zmian w Regulaminie.
          O wszelkich zmianach użytkownicy zostaną poinformowani poprzez komunikat
          na Platformie lub wiadomość e-mail. Kontynuowanie korzystania z Platformy
          po wprowadzeniu zmian jest równoznaczne z ich akceptacją.
        </p>
      </section>

      <section className="mb-8">
        <h2 className="mb-4 text-2xl font-semibold">9. Postanowienia końcowe</h2>
        <p className="mb-4 text-gray-700">
          <strong>9.1.</strong> W sprawach nieuregulowanych niniejszym Regulaminem
          zastosowanie mają przepisy prawa polskiego, w szczególności Kodeksu Cywilnego
          oraz ustawy o świadczeniu usług drogą elektroniczną.
        </p>
        <p className="mb-4 text-gray-700">
          <strong>9.2.</strong> Wszelkie spory wynikające z korzystania z Platformy będą
          rozstrzygane przez sądy właściwe dla siedziby Administratora.
        </p>
        <p className="mb-4 text-gray-700">
          <strong>9.3.</strong> W przypadku pytań dotyczących Regulaminu, prosimy o kontakt
          na adres: <strong>[e-mail kontaktowy]</strong>.
        </p>
      </section>
    </LegalPageLayout>
  );
}

