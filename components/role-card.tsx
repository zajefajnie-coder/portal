import { Card, CardContent, CardHeader } from "@/components/ui/card";
import { ReactNode } from "react";

interface RoleCardProps {
  icon: ReactNode;
  title: string;
  description: string;
}

export function RoleCard({ icon, title, description }: RoleCardProps) {
  return (
    <Card className="h-full transition-shadow hover:shadow-lg">
      <CardHeader>
        <div className="mb-4 text-primary">{icon}</div>
        <h3 className="text-2xl font-semibold">{title}</h3>
      </CardHeader>
      <CardContent>
        <p className="text-muted-foreground">{description}</p>
      </CardContent>
    </Card>
  );
}

