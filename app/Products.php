<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Products extends Model
{
    use SoftDeletes;
    public $table = 'Products';

    protected $fillable = [
        'name', 'description','category_id'
    ];

    public function category()
    {
        return $this->hasOne('App\Category','category_id');
    }
}
