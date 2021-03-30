<?php

namespace Dolphin\Ting\Http\Model;

use Illuminate\Database\Eloquent\Model;

class SecondhandGoods extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'secondhand_goods';

    public $guarded = ['id'];

    protected $casts = [
        'id'          => 'int',
        'uid'         => 'int',
        'status'      => 'int',
        'price'       => 'float',
        'describe'    => 'string',
    ];
}