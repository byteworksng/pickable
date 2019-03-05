<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{

    protected $fillable = ['id', 'email', 'groups', 'products'];

    public function map(array $attributes){
            return [
                'id' => $attributes['Id'] ?? null ,
                'products' => $attributes['_Products'] ?? null,
                'groups' => $attributes['Groups'] ?? null,
                'email' => $attributes['Email'] ?? null,
            ];
    }


    public function __construct(array $attributes = [])
    {
        parent::__construct($this->map($attributes));
    }
}
