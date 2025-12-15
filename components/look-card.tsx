import Image from "next/image";
import { Card, CardContent, CardFooter, CardHeader } from "@/components/ui/card";
import { Look } from "@/lib/mock-data";
import Link from "next/link";

interface LookCardProps {
  look: Look;
}

export function LookCard({ look }: LookCardProps) {
  return (
    <Card className="group overflow-hidden transition-shadow hover:shadow-lg">
      <Link href={`/look/${look.id}`} className="block">
        <div className="relative aspect-[3/4] w-full overflow-hidden bg-gray-100">
          <Image
            src={look.image_url}
            alt={look.image_alt}
            fill
            className="object-cover transition-transform duration-300 group-hover:scale-105"
            sizes="(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 33vw"
          />
        </div>
        <CardHeader>
          <h3 className="text-xl font-semibold">{look.title}</h3>
          {look.location && (
            <p className="text-sm text-muted-foreground">{look.location}</p>
          )}
        </CardHeader>
        <CardContent>
          <p className="text-sm text-muted-foreground">
            Autor: <span className="font-medium">{look.author_name}</span>
          </p>
        </CardContent>
        <CardFooter className="flex flex-wrap gap-2">
          {look.tags.map((tag) => (
            <span
              key={tag}
              className="rounded-full bg-secondary px-2 py-1 text-xs text-secondary-foreground"
            >
              #{tag}
            </span>
          ))}
        </CardFooter>
      </Link>
    </Card>
  );
}

