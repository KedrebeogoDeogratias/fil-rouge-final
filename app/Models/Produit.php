<?php

namespace App\Models;

use ApiPlatform\Metadata\ApiResource;
use Illuminate\Database\Eloquent\Model;

#[ApiResource(
    formats: ['json'],
    paginationEnabled: false,
    rules: \App\Http\Requests\ProduitRequest::class
)]
class Produit extends Model
{
    protected $fillable = [
        'designation_p',
        'type_p',
        'prix_ht',
        'promotion_p',
        'stock_p',
        'image_p',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = [
        'prix_ht'     => 'float',
        'promotion_p' => 'integer',
        'stock_p'     => 'integer',
    ];
}