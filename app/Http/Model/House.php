<?php

namespace Dolphin\Ting\Http\Model;

use Illuminate\Database\Eloquent\Model;

class House extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'houses';

    public $guarded = ['id'];

    protected $casts = [
        'id'          => 'int',
        'uid'         => 'int',
        'status'      => 'int',
        'price'       => 'float',
        'describe'    => 'string',
    ];
}