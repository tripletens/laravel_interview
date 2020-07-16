<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Categories extends Model
{
    use SoftDeletes;
    
    public $table = 'Categories';

    protected $fillable = [
        'name', 'description'
    ];

    public function products()
    {
        return $this->belongsTo('App\Products');
    }
}
