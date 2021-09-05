<?php

namespace Dolphin\Ting\Http\Model;

use Illuminate\Database\Eloquent\Model;

class Rent extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'rent';

    public $guarded = ['id'];

    protected $casts = [
        'id'     => 'int',
        'uid'    => 'int',
        'status' => 'int',
        'price'  => 'string',
        'desc'   => 'string',
    ];
}