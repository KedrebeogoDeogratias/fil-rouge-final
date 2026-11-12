# Fil rouge

API de gestion de produits développée avec Laravel 12, API Platform et
Laravel Sanctum. Elle est accompagnée d'un catalogue web premium (thème sombre
glassmorphique) avec gestion des rôles administrateur et utilisateur.

## État d'avancement

### Réalisé

- [x] Initialisation de l'application Laravel.
- [x] Configuration PHP 8.2+, SQLite et Vite.
- [x] Modèles `Produit` et `Categorie` avec une relation `BelongsTo` /
  `HasMany` (`categorie_id` remplace `type_p`).
- [x] Migrations des tables `categories` et `produits`, avec clé étrangère
  `categorie_id` (`nullOnDelete`) et index sur `designation_c`.
- [x] Exposition des modèles `Produit` et `Categorie` par API Platform.
- [x] Opérations CRUD générées par API Platform pour les deux ressources :
  `GET`, `POST`, `PATCH` et `DELETE`.
- [x] Lecture publique, écriture réservée aux administrateurs via Sanctum.
- [x] Pagination fixée à 30 éléments par page.
- [x] Filtres et tri sur les produits et les catégories.
- [x] Validation des données via `ProduitRequest` et `CategorieRequest`.
- [x] Import des catégories et des produits depuis `database/produits.json`
  avec des seeders utilisant `upsert` pour éviter les doublons.
- [x] Authentification Sanctum implémentée dans `AuthController` :
  inscription, connexion, récupération de l'utilisateur courant et
  déconnexion.
- [x] Rôles `admin` / `user` sur les utilisateurs et middleware
  `EnsureUserIsAdmin` (401 sans token, 403 pour un utilisateur non admin).
- [x] Gestion des utilisateurs par les administrateurs
  (`AdminUserController`) : création, changement de rôle, suppression.
- [x] Protection contre l'auto-rétrogradation et l'auto-suppression d'un
  administrateur (422).
- [x] Seeders `DatabaseSeeder` et `ProduitSeeder` : catégories, produits et
  comptes de démonstration.
- [x] Documentation OpenAPI/Swagger disponible avec API Platform.
- [x] Interface web premium (Blade + Tailwind + JavaScript) consommant l'API :
  recherche, catégories, prix, stock, promotions, tri, pagination, connexion,
  inscription et espace d'administration.
- [x] Tests fonctionnels des droits d'accès et de l'authentification :
  **7 tests, 18 assertions**.

### À finaliser

- [ ] Compléter la couverture de tests des filtres et de la pagination.

## Technologies

- PHP 8.2+
- Laravel 12
- API Platform Laravel 5
- Laravel Sanctum
- SQLite
- Tailwind CSS 4 + Vite
- PHPUnit

## Installation

### Prérequis

- PHP 8.2 ou supérieur
- Composer
- Node.js et npm

### Installation automatique

```bash
composer run setup
```

Cette commande installe les dépendances PHP et JavaScript, crée la clé
d'application, exécute les migrations, importe les données et construit les
assets frontend.

### Installation manuelle

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
npm install
npm run build
```

## Lancement en développement

```bash
composer run dev
```

Ou séparément :

```bash
php artisan serve
```

L'application est ensuite accessible aux adresses suivantes :

- `http://localhost:8000/` : catalogue web (filtres, tri, pagination) ;
- `http://localhost:8000/api/docs` : documentation Swagger de l'API.

## Comptes de démonstration

| Rôle | Email | Mot de passe |
| --- | --- | --- |
| Administrateur | `admin@filrouge.test` | `password` |
| Utilisateur | `user@filrouge.test` | `password` |

Le compte administrateur peut être personnalisé avec les variables
d'environnement `ADMIN_EMAIL` et `ADMIN_PASSWORD`.

## Interface web

La page `/` (`resources/views/catalogue.blade.php`) consomme l'API publique en
JavaScript et permet de :

- rechercher un produit par désignation ;
- filtrer par catégorie, prix HT min/max, stock minimum et promotions ;
- trier par désignation, prix, stock ou promotion ;
- naviguer dans les résultats paginés ;
- créer un compte, se connecter et restaurer sa session via un token Sanctum
  stocké dans le navigateur ;
- accéder, pour les administrateurs, à un espace de gestion des produits, des
  catégories et des utilisateurs.

Les visiteurs et les utilisateurs standards disposent d'un accès en lecture.
Les écritures sur les produits et les catégories, ainsi que la gestion des
utilisateurs, sont réservées aux administrateurs.

## API disponible

### Produits

API Platform expose les endpoints suivants sous `/api/produits` :

| Méthode | Endpoint | Description | Accès |
| --- | --- | --- | --- |
| `GET` | `/api/produits` | Liste des produits | Public |
| `GET` | `/api/produits/{id}` | Consulte un produit | Public |
| `POST` | `/api/produits` | Crée un produit | Admin |
| `PATCH` | `/api/produits/{id}` | Modifie un produit | Admin |
| `DELETE` | `/api/produits/{id}` | Supprime un produit | Admin |

Un administrateur doit fournir un token Sanctum
(`Authorization: Bearer <token>`). Les listes sont paginées à **30 éléments par
page** (`?page=2` pour la page suivante).

Filtres et tri disponibles (paramètres en camelCase) :

| Paramètre | Effet | Exemple |
| --- | --- | --- |
| `designationP` | Recherche partielle sur la désignation | `?designationP=clavier` |
| `categorieId` | Égalité sur `categorie_id` | `?categorieId=4` |
| `categorieDesignation` | Recherche partielle sur `categorie.designation_c` | `?categorieDesignation=mobilier` |
| `prixHt[gte]` / `prixHt[lte]` | Prix HT minimum / maximum | `?prixHt[gte]=100&prixHt[lte]=200` |
| `stockP[gte]` | Stock minimum | `?stockP[gte]=5` |
| `promotionP[gt]` | Promotion strictement supérieure à | `?promotionP[gt]=0` |
| `sort[<property>]` | Tri `asc` / `desc` (`designationP`, `prixHt`, `stockP`, `promotionP`) | `?sort[prixHt]=desc` |

### Catégories

API Platform expose les endpoints suivants sous `/api/categories` :

| Méthode | Endpoint | Description | Accès |
| --- | --- | --- | --- |
| `GET` | `/api/categories` | Liste des catégories | Public |
| `GET` | `/api/categories/{id}` | Consulte une catégorie | Public |
| `POST` | `/api/categories` | Crée une catégorie | Admin |
| `PATCH` | `/api/categories/{id}` | Modifie une catégorie | Admin |
| `DELETE` | `/api/categories/{id}` | Supprime une catégorie | Admin |

Filtres disponibles : `?designationC=...` (recherche partielle) et
`?sort[designationC]=asc|desc`.

Les données sont échangées au format JSON. La documentation interactive est
accessible via [`/api/docs`](http://localhost:8000/api/docs).

### Authentification et utilisateurs

Les routes définies dans `routes/api.php` sont :

| Méthode | Endpoint | Accès |
| --- | --- | --- |
| `POST` | `/api/register` | Public (crée toujours un rôle `user`) |
| `POST` | `/api/login` | Public |
| `GET` | `/api/me` | Token Sanctum requis |
| `POST` | `/api/logout` | Token Sanctum requis |
| `GET` | `/api/users` | Admin |
| `GET` | `/api/users/{id}` | Admin |
| `POST` | `/api/admin/users` | Admin |
| `PATCH` | `/api/admin/users/{id}` | Admin |
| `DELETE` | `/api/admin/users/{id}` | Admin |

Un administrateur ne peut ni retirer son propre rôle administrateur ni
supprimer son propre compte (réponse `422`).

Après connexion, le token doit être transmis dans l'en-tête :

```http
Authorization: Bearer <token>
```

## Validation d'un produit

Les champs attendus sont :

```json
{
  "designationP": "Produit exemple",
  "categorie": "/api/categories/4",
  "prixHt": 100.00,
  "promotionP": 10,
  "stockP": 25,
  "imageP": "data:image/png;base64,..."
}
```

`designationP`, `categorie` (IRI), `prixHt` et `stockP` sont obligatoires à la
création. En `PATCH`, tous les champs deviennent optionnels. La promotion doit
être comprise entre 0 et 100. Une catégorie se crée avec
`{"designationC": "Informatique"}` (unique).

## Base de données et données initiales

La base de données par défaut est SQLite (`database/database.sqlite`).
Pour importer les données et créer les comptes de démonstration :

```bash
php artisan db:seed
```

`DatabaseSeeder` crée les comptes administrateur et utilisateur, puis appelle
`ProduitSeeder`, qui crée les catégories distinctes de `type_p`, les associe aux
produits via `categorie_id` et met à jour les produits existants au lieu de
créer des doublons.

## Tests

```bash
php artisan test
```

État vérifié au dernier contrôle : **7 tests passants, 18 assertions**.

## Structure principale

```text
app/
├── Http/Controllers/       # Contrôleurs HTTP (auth, admin)
├── Http/Middleware/        # EnsureUserIsAdmin
├── Http/Requests/          # Validation des requêtes
└── Models/                 # Modèles Eloquent et ressources API
database/
├── migrations/             # Structure de la base de données
├── produits.json           # Données produits initiales
└── seeders/                # Données initiales et comptes de démo
resources/
├── css/app.css             # Thème premium (dark / glassmorphique)
├── js/catalogue.js         # Catalogue, authentification et administration
└── views/catalogue.blade.php
routes/
├── api.php                 # Routes API d'authentification et d'administration
└── web.php                 # Route web d'accueil
```

## Licence

Projet réalisé dans le cadre du fil rouge. Licence à préciser.
