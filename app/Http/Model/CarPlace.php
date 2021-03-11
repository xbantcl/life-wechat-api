<?php

namespace Dolphin\Ting\Http\Model;

use Illuminate\Database\Eloquent\Model;

class CarPlace extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'car_places';

    public $guarded = ['id'];

    protected $casts = [
        'id'          => 'int',
        'uid'         => 'int',
        'status'      => 'int',
        'price'       => 'float',
        'describe'    => 'string',
    ];
}