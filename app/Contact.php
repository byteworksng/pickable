<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{

    protected $fillable = ['id', 'email', 'groups', 'products'];

    public function map(array $attributes){
            return [
                'id' => $attributes['Id'],
                'products' => $attributes['_Products'],
                'groups' => $attributes['Groups'],
                'email' => $attributes['Email'],
            ];
    }


    public function __construct(array $attributes = [])
    {
        parent::__construct($this->map($attributes));
    }
}
