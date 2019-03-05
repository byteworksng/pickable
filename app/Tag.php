<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{

    protected $table = 'tags';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'description', 'name', 'tags_category_id'
    ];

    public function tags_category()
    {
        return $this->belongsTo(TagCategory::class, 'tags_category_id');
    }
}
