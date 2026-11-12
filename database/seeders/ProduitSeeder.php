<?php

namespace Database\Seeders;

use App\Models\Categorie;
use App\Models\Produit;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ProduitSeeder extends Seeder
{
    public function run(): void
    {
        $donnees = json_decode(
            File::get(database_path('./produits.json')),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $maintenant = now();

        $categories = collect($donnees)
            ->pluck('type_p')
            ->filter()
            ->unique()
            ->values()
            ->map(fn (string $designation) => [
                'designation_c' => $designation,
                'created_at'    => $maintenant,
                'updated_at'    => $maintenant,
            ])
            ->all();

        Categorie::upsert($categories, ['designation_c'], ['updated_at']);

        $categorieIds = Categorie::pluck('id', 'designation_c');

        $lignes = array_map(fn (array $p) => [
            'id'            => (int) $p['id_p'],
            'designation_p' => $p['designation_p'],
            'categorie_id'  => $categorieIds[$p['type_p']] ?? null,
            'prix_ht'       => $p['prix_ht'],
            'promotion_p'   => $p['promotion_p'] ?? 0,
            'stock_p'       => $p['stock_p'] ?? 0,
            'image_p'       => $p['image_p'] ?: null,
            'created_at'    => isset($p['date_in']) ? Carbon::parse($p['date_in']) : now(),
            'updated_at'    => isset($p['times_in']) ? Carbon::parse($p['times_in']) : now(),
        ], $donnees);

        // upsert : relancer le seeder met à jour au lieu de dupliquer
        Produit::upsert(
            $lignes,
            ['id'],
            ['designation_p', 'categorie_id', 'prix_ht', 'promotion_p', 'stock_p', 'image_p', 'created_at', 'updated_at']
        );
    }
}
