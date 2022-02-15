<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int    id
 * @property string name
 * @property string image
 */
class ProductCategory extends Model
{
    use HasFactory;

	/**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name', 'image',
    ];

	/**
	 * Get all of the product for the ProductCategory
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function product(): HasMany
	{
		return $this->hasMany(Product::class, 'category_id', 'id');
	}
}
