<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produits', function (Blueprint $table) {
            $table->foreignId('categorie_id')
                ->nullable()
                ->after('id')
                ->constrained('categories')
                ->nullOnDelete();
        });

        $types = DB::table('produits')
            ->whereNotNull('type_p')
            ->distinct()
            ->pluck('type_p');

        foreach ($types as $type) {
            $categorieId = DB::table('categories')
                ->where('designation_c', $type)
                ->value('id');

            if (! $categorieId) {
                $categorieId = DB::table('categories')->insertGetId([
                    'designation_c' => $type,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('produits')
                ->where('type_p', $type)
                ->update(['categorie_id' => $categorieId]);
        }

        Schema::table('produits', function (Blueprint $table) {
            $table->dropIndex(['type_p']);
        });

        Schema::table('produits', function (Blueprint $table) {
            $table->dropColumn('type_p');
        });
    }

    public function down(): void
    {
        Schema::table('produits', function (Blueprint $table) {
            $table->string('type_p')->nullable()->after('designation_p');
        });

        DB::table('produits')
            ->join('categories', 'categories.id', '=', 'produits.categorie_id')
            ->update(['produits.type_p' => DB::raw('categories.designation_c')]);

        Schema::table('produits', function (Blueprint $table) {
            $table->dropConstrainedForeignId('categorie_id');
            $table->index('type_p');
        });
    }
};
