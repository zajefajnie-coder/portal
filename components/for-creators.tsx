import { RoleCard } from "./role-card";
import { Camera, Users, Palette } from "lucide-react";

export function ForCreators() {
  return (
    <section className="bg-gray-50 py-16 md:py-24" id="dla-tworcow" aria-labelledby="dla-tworcow-heading">
      <div className="container mx-auto px-4">
        <h2 className="mb-12 text-center text-3xl font-bold md:text-4xl" id="dla-tworcow-heading">
          Dla Wszystkich Twórców
        </h2>
        <div className="grid grid-cols-1 gap-8 md:grid-cols-3">
          <RoleCard
            icon={<Camera className="h-8 w-8" />}
            title="Fotografowie"
            description="Prezentuj sesje, znajdź modeli, twórz teamy. Buduj portfolio i rozwijaj swoją karierę w branży fotograficznej."
          />
          <RoleCard
            icon={<Users className="h-8 w-8" />}
            title="Modelki i Modele"
            description="Buduj portfolio, aplikuj na zlecenia, rozwijaj karierę. Połącz się z fotografami i twórcami z całej Polski."
          />
          <RoleCard
            icon={<Palette className="h-8 w-8" />}
            title="Zespół Kreatywny"
            description="Pokaż swoje umiejętności: wizaż, fryzura, styling, retusz — współpracuj z całym środowiskiem fotomodelingu."
          />
        </div>
      </div>
    </section>
  );
}

