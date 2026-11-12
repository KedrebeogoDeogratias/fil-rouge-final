<?php

namespace Tests\Feature;

use App\Models\Categorie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    private function productPayload(Categorie $categorie): array
    {
        return [
            'designationP' => 'Produit de test',
            'categorie'    => '/api/categories/'.$categorie->id,
            'prixHt'       => 49.9,
            'promotionP'   => 0,
            'stockP'       => 5,
        ];
    }

    public function test_guest_cannot_create_a_product(): void
    {
        $categorie = Categorie::create(['designation_c' => 'Test']);

        $this->postJson('/api/produits', $this->productPayload($categorie))
            ->assertStatus(401);
    }

    public function test_regular_user_cannot_write_or_manage_users(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $categorie = Categorie::create(['designation_c' => 'Test']);

        Sanctum::actingAs($user);

        $this->postJson('/api/produits', $this->productPayload($categorie))
            ->assertStatus(403);

        $this->getJson('/api/users')->assertStatus(403);

        $this->postJson('/api/admin/users', [
            'name'     => 'Intrus',
            'email'    => 'intrus@example.com',
            'password' => 'password123',
            'role'     => 'admin',
        ])->assertStatus(403);
    }

    public function test_admin_can_create_a_product_and_manage_users(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $categorie = Categorie::create(['designation_c' => 'Test']);

        Sanctum::actingAs($admin);

        $this->postJson('/api/produits', $this->productPayload($categorie))
            ->assertStatus(201)
            ->assertJsonPath('designationP', 'Produit de test');

        $this->assertDatabaseHas('produits', ['designation_p' => 'Produit de test']);

        $this->getJson('/api/users')->assertOk();

        $this->postJson('/api/admin/users', [
            'name'     => 'Nouvel admin',
            'email'    => 'nouvel.admin@example.com',
            'password' => 'password123',
            'role'     => 'admin',
        ])->assertStatus(201)
          ->assertJsonPath('role', 'admin');
    }

    public function test_registration_always_creates_a_regular_user(): void
    {
        $this->postJson('/api/register', [
            'name'                  => 'Nouveau membre',
            'email'                 => 'membre@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ])->assertStatus(201)
          ->assertJsonPath('user.role', 'user');
    }

    public function test_admin_cannot_demote_or_delete_itself(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Sanctum::actingAs($admin);

        $this->patchJson('/api/admin/users/'.$admin->id, ['role' => 'user'])
            ->assertStatus(422);

        $this->deleteJson('/api/admin/users/'.$admin->id)
            ->assertStatus(422);

        $this->assertDatabaseHas('users', [
            'id'   => $admin->id,
            'role' => 'admin',
        ]);
    }
}
