// Types for mock data
export interface Look {
  id: string;
  title: string;
  date: string;
  location?: string;
  image_url: string;
  image_alt: string;
  author_id: string;
  author_name: string;
  tags: string[];
  collaborators?: string[];
}

export const mockLooks: Look[] = [
  {
    id: '1',
    title: 'Sesja portretowa – letnia',
    date: '2024-08-15',
    location: 'Warszawa',
    image_url: 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?w=800&h=1200&fit=crop',
    image_alt: 'Kobieta w białej sukience na tle zielonego lasu, naturalne światło',
    author_id: 'photographer-1',
    author_name: 'Anna Kowalska',
    tags: ['portret', 'natura', 'letnia', 'kobieta'],
    collaborators: ['Modelka: Maria Nowak', 'Wizażystka: Katarzyna Wiśniewska'],
  },
  {
    id: '2',
    title: 'E-commerce – biżuteria',
    date: '2024-09-20',
    location: 'Kraków',
    image_url: 'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=800&h=1200&fit=crop',
    image_alt: 'Elegancka biżuteria na białym tle, profesjonalne studio',
    author_id: 'photographer-2',
    author_name: 'Piotr Zieliński',
    tags: ['e-commerce', 'biżuteria', 'studio', 'produkt'],
    collaborators: ['Stylista: Joanna Krawczyk'],
  },
  {
    id: '3',
    title: 'Moda – studio',
    date: '2024-10-05',
    location: 'Wrocław',
    image_url: 'https://images.unsplash.com/photo-1488426862026-3ee34a7d66df?w=800&h=1200&fit=crop',
    image_alt: 'Modelka w eleganckiej sukience wieczorowej na czarnym tle studio',
    author_id: 'photographer-3',
    author_name: 'Magdalena Lewandowska',
    tags: ['moda', 'studio', 'elegancja', 'czarno-białe'],
    collaborators: ['Model: Jakub Kowalczyk', 'Fryzjer: Tomasz Nowak', 'Retuszer: Aleksandra Szymańska'],
  },
];

