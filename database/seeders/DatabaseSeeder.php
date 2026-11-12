<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@filrouge.test')],
            [
                'name'     => 'Administrateur',
                'password' => env('ADMIN_PASSWORD', 'password'),
                'role'     => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@filrouge.test'],
            [
                'name'     => 'Utilisateur démo',
                'password' => 'password',
                'role'     => 'user',
            ]
        );

        $this->call(ProduitSeeder::class);
    }
}
