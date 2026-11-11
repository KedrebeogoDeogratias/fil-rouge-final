# Fil rouge

API de gestion de produits développée avec Laravel 12, API Platform et
Laravel Sanctum.

## État d'avancement

### Réalisé

- [x] Initialisation de l'application Laravel.
- [x] Configuration PHP 8.2+, SQLite et Vite.
- [x] Modèle `Produit` avec les champs suivants :
  `designation_p`, `type_p`, `prix_ht`, `promotion_p`, `stock_p` et `image_p`.
- [x] Migration de la table `produits`, avec index sur le type de produit.
- [x] Exposition du modèle `Produit` par API Platform.
- [x] Opérations CRUD produits générées par API Platform :
  `GET`, `POST`, `PATCH` et `DELETE`.
- [x] Validation des données produit via `ProduitRequest`.
- [x] Import des produits depuis `database/produits.json` avec un seeder
  utilisant `upsert` pour éviter les doublons.
- [x] Authentification Sanctum implémentée dans `AuthController` :
  inscription, connexion, récupération de l'utilisateur courant et
  déconnexion.
- [x] Documentation OpenAPI/Swagger disponible avec API Platform.
- [x] Tests de base présents et actuellement passants : **2 tests, 2 assertions**.

### En cours / à finaliser

- [ ] Enregistrer `routes/api.php` dans `bootstrap/app.php`. Les routes
  `/register`, `/login`, `/me` et `/logout` sont écrites mais ne sont pas
  actuellement chargées par Laravel.
- [ ] Ajouter `ProduitSeeder` à `DatabaseSeeder` afin que les produits soient
  insérés lors d'un `php artisan db:seed`.
- [ ] Ajouter des tests d'intégration pour les endpoints produits et
  authentification.
- [ ] Définir les règles d'accès : les opérations produits sont actuellement
  exposées sans middleware d'authentification.
- [ ] Remplacer les tests d'exemple par une couverture fonctionnelle du projet.

## Technologies

- PHP 8.2+
- Laravel 12
- API Platform Laravel 5
- Laravel Sanctum
- MySQL
- PHPUnit

## Installation

### Prérequis

- PHP 8.2 ou supérieur
- Composer


### Installation automatique

```bash
composer run setup
```

Cette commande installe les dépendances PHP et JavaScript, crée la clé
d'application, exécute les migrations et construit les assets frontend.

### Installation manuelle

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate
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
npm run dev
```

L'application est ensuite accessible à l'adresse
`http://localhost:8000`.

## API disponible

### Produits

API Platform expose les endpoints suivants sous `/api/produits` :

| Méthode | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/api/produits` | Liste des produits |
| `GET` | `/api/produits/{id}` | Consulte un produit |
| `POST` | `/api/produits` | Crée un produit |
| `PATCH` | `/api/produits/{id}` | Modifie un produit |
| `DELETE` | `/api/produits/{id}` | Supprime un produit |

Les données sont échangées au format JSON. La documentation interactive est
accessible via [`/api/docs`](http://localhost:8000/api/docs).

### Authentification prévue

Les routes prévues dans `routes/api.php` sont :

| Méthode | Endpoint | Accès |
| --- | --- | --- |
| `POST` | `/api/register` | Public |
| `POST` | `/api/login` | Public |
| `GET` | `/api/me` | Token Sanctum requis |
| `POST` | `/api/logout` | Token Sanctum requis |

Après connexion, le token doit être transmis dans l'en-tête :

```http
Authorization: Bearer <token>
```

## Validation d'un produit

Les champs attendus sont :

```json
{
  "designationP": "Produit exemple",
  "typeP": "Catégorie",
  "prixHt": 100.00,
  "promotionP": 10,
  "stockP": 25,
  "imageP": "data:image/png;base64,..."
}
```

`designationP`, `typeP`, `prixHt` et `stockP` sont obligatoires.
La promotion doit être comprise entre 0 et 100.

## Base de données et données initiales

La base de données par défaut est SQLite (`database/database.sqlite`).
Pour importer les données du fichier JSON :

```bash
php artisan db:seed --class=ProduitSeeder
```

Le seeder met à jour les produits existants au lieu de créer des doublons.

## Tests

```bash
php artisan test
```

État vérifié au dernier contrôle : **2 tests passants, 2 assertions**.

## Structure principale

```text
app/
├── Http/Controllers/       # Contrôleurs HTTP, dont l'authentification
├── Http/Requests/          # Validation des requêtes
└── Models/                 # Modèles Eloquent et ressources API
database/
├── migrations/             # Structure de la base de données
├── produits.json           # Données produits initiales
└── seeders/                # Import des données initiales
routes/
├── api.php                 # Routes API d'authentification
└── web.php                 # Route web d'accueil
```

## Licence

Projet réalisé dans le cadre du fil rouge. Licence à préciser.
