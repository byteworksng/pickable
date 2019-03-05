<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TagCategory extends Model
{
    protected $table = 'tags_category';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'description', 'name',
    ];

}
