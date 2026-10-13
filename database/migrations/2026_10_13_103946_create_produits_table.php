<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produits', function (Blueprint $table) {
            $table->id();                                        // exposé sous "id"
            $table->string('designation_p', 255);
            $table->string('type_p');
            $table->decimal('prix_ht', 10, 2)->default(0);
            $table->unsignedTinyInteger('promotion_p')->default(0);
            $table->unsignedInteger('stock_p')->default(0);
            $table->longText('image_p')->nullable();             // data URL base64
            $table->timestamps();

            $table->index('type_p');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produits');
    }
};