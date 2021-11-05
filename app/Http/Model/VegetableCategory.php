<?php

namespace Dolphin\Ting\Http\Model;

use Illuminate\Database\Eloquent\Model;

class VegetableCategory extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'vegetable_categories';

    public $guarded = ['id'];

    protected $casts = [
        'id'     => 'int',
        'uid'    => 'int',
        'name' => 'string',
    ];
}