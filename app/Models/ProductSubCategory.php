<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int    id
 * @property int 	parent_id
 * @property string name
 * @property string image
 */
class ProductSubCategory extends Model
{
    use HasFactory;

	/**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name', 'image', 'parent_id'
    ];

	public function productCategory()
	{
		return $this->belongsTo(ProductCategory::class, 'parent_id');
	}
}
