<?php

namespace Dolphin\Ting\Http\Model;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'products';

    public $guarded = ['id'];

    protected $casts = [
        'id'     => 'int',
        'name' => 'string',
        'image'  => 'string',
    ];
}