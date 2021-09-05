<?php

namespace Dolphin\Ting\Http\Model;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'address';

    public $guarded = ['id'];

    protected $casts = [
        'id'     => 'int',
        'uid'    => 'int',
        'name' => 'string',
        'mobile'  => 'string',
        'gps_address'  => 'string',
        'address' => 'string',
        'mark'    => 'string',
        'is_default' => 'int'
    ];
}