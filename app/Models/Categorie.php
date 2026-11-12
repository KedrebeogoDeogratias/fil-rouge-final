<?php

namespace App\Models;

use ApiPlatform\Laravel\Eloquent\Filter\OrderFilter;
use ApiPlatform\Laravel\Eloquent\Filter\PartialSearchFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\QueryParameter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ApiResource(
    formats: ['json'],
    paginationEnabled: true,
    paginationItemsPerPage: 30,
    rules: \App\Http\Requests\CategorieRequest::class,
    operations: [
        new GetCollection(),
        new Get(uriTemplate: '/categories/{id}'),
        new Post(middleware: ['auth:sanctum', 'admin']),
        new Patch(uriTemplate: '/categories/{id}', middleware: ['auth:sanctum', 'admin']),
        new Delete(uriTemplate: '/categories/{id}', middleware: ['auth:sanctum', 'admin']),
    ],
)]
#[QueryParameter(key: 'designationC', filter: PartialSearchFilter::class, property: 'designation_c')]
#[QueryParameter(key: 'sort[:property]', filter: OrderFilter::class, properties: ['designation_c'])]
class Categorie extends Model
{
    protected $fillable = [
        'designation_c',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function produits(): HasMany
    {
        return $this->hasMany(Produit::class);
    }
}
