<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int    id
 * @property string name
 * @property string email
 * @property string subject
 * @property string message
 */
class Contact extends Model
{
    use HasFactory;
	
	/**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name', 'email', 'subject', 'message',
    ];
}

