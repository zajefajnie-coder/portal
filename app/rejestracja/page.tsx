"use client";

import { useState } from "react";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Checkbox } from "@/components/ui/checkbox";
import { Label } from "@/components/ui/label";
import Link from "next/link";

export default function RejestracjaPage() {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [acceptedTerms, setAcceptedTerms] = useState(false);
  const [error, setError] = useState("");

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError("");

    if (!acceptedTerms) {
      setError("Musisz zaakceptować Regulamin oraz Politykę Prywatności");
      return;
    }

    // TODO: Implement actual registration logic with NextAuth and MySQL
    console.log("Registration:", { email, password, acceptedTerms });
  };

  return (
    <main className="min-h-screen bg-gray-50 py-12 md:py-16">
      <div className="container mx-auto px-4">
        <Card className="mx-auto max-w-md">
          <CardHeader>
            <CardTitle className="text-2xl">Utwórz konto</CardTitle>
            <CardDescription>
              Dołącz do społeczności portal-modelingowy.pl
            </CardDescription>
          </CardHeader>
          <CardContent>
            <form onSubmit={handleSubmit} className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="email">Adres e-mail</Label>
                <input
                  id="email"
                  type="email"
                  required
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                  placeholder="twoj@email.pl"
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="password">Hasło</Label>
                <input
                  id="password"
                  type="password"
                  required
                  minLength={8}
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                  placeholder="Minimum 8 znaków"
                />
              </div>

              <div className="flex items-start space-x-2">
                <Checkbox
                  id="terms"
                  checked={acceptedTerms}
                  onCheckedChange={(checked) => setAcceptedTerms(checked === true)}
                  required
                  aria-required="true"
                />
                <Label
                  htmlFor="terms"
                  className="text-sm leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                >
                  Akceptuję{" "}
                  <Link href="/regulamin" className="text-primary underline">
                    Regulamin
                  </Link>{" "}
                  oraz{" "}
                  <Link href="/polityka-prywatnosci" className="text-primary underline">
                    Politykę Prywatności
                  </Link>{" "}
                  portalu-modelingowy.pl
                </Label>
              </div>

              {error && (
                <div className="rounded-md bg-destructive/10 p-3 text-sm text-destructive">
                  {error}
                </div>
              )}

              <Button type="submit" className="w-full">
                Utwórz konto
              </Button>

              <div className="relative">
                <div className="absolute inset-0 flex items-center">
                  <span className="w-full border-t" />
                </div>
                <div className="relative flex justify-center text-xs uppercase">
                  <span className="bg-card px-2 text-muted-foreground">lub</span>
                </div>
              </div>

              <Button type="button" variant="outline" className="w-full">
                Zaloguj się przez Google
              </Button>

              <p className="text-center text-sm text-muted-foreground">
                Masz już konto?{" "}
                <Link href="/logowanie" className="text-primary underline">
                  Zaloguj się
                </Link>
              </p>
            </form>
          </CardContent>
        </Card>
      </div>
    </main>
  );
}

