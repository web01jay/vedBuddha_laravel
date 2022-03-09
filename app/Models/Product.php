<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int    id
 * @property int    category_id
 * @property int    sub_category_id
 * @property string name
 * @property string description
 * @property string image
 */
class Product extends Model
{
    use HasFactory;

	/**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'category_id', 'name', 'description', 'image', 'sub_category_id'
    ];
	
	public function category()
	{
		return $this->belongsTo(ProductCategory::class, 'category_id');
	}

	public function subCategory()
	{
		return $this->belongsTo(ProductSubCategory::class, 'sub_category_id');
	}

}
