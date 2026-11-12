<?php

namespace App\Models;

use ApiPlatform\Laravel\Eloquent\Filter\EqualsFilter;
use ApiPlatform\Laravel\Eloquent\Filter\OrderFilter;
use ApiPlatform\Laravel\Eloquent\Filter\PartialSearchFilter;
use ApiPlatform\Laravel\Eloquent\Filter\RangeFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\QueryParameter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ApiResource(
    formats: ['json'],
    paginationEnabled: true,
    paginationItemsPerPage: 30,
    rules: \App\Http\Requests\ProduitRequest::class,
    operations: [
        new GetCollection(),
        new Get(uriTemplate: '/produits/{id}'),
        new Post(middleware: ['auth:sanctum', 'admin']),
        new Patch(uriTemplate: '/produits/{id}', middleware: ['auth:sanctum', 'admin']),
        new Delete(uriTemplate: '/produits/{id}', middleware: ['auth:sanctum', 'admin']),
    ],
)]
#[QueryParameter(key: 'designationP', filter: PartialSearchFilter::class, property: 'designation_p')]
#[QueryParameter(key: 'categorieId', filter: EqualsFilter::class, property: 'categorie_id')]
#[QueryParameter(key: 'categorieDesignation', filter: PartialSearchFilter::class, property: 'categorie.designation_c')]
#[QueryParameter(key: 'prixHt', filter: RangeFilter::class, property: 'prix_ht')]
#[QueryParameter(key: 'stockP', filter: RangeFilter::class, property: 'stock_p')]
#[QueryParameter(key: 'promotionP', filter: RangeFilter::class, property: 'promotion_p')]
#[QueryParameter(key: 'sort[:property]', filter: OrderFilter::class, properties: ['designation_p', 'prix_ht', 'stock_p', 'promotion_p'])]
class Produit extends Model
{
    protected $fillable = [
        'designation_p',
        'categorie_id',
        'prix_ht',
        'promotion_p',
        'stock_p',
        'image_p',
    ];

    protected $hidden = ['categorie_id', 'created_at', 'updated_at'];

    protected $casts = [
        'prix_ht'     => 'float',
        'promotion_p' => 'integer',
        'stock_p'     => 'integer',
    ];

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class);
    }
}
