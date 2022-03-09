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

	public function product()
	{
		return $this->hasMany(Product::class, 'category_id', 'id');
	}
}
