<?php

namespace Dolphin\Ting\Http\Model;

use Illuminate\Database\Eloquent\Model;

class Recycle extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'recycle_order';

    public $guarded = ['id'];

    protected $casts = [
        'id'               => 'int',
        'uid'              => 'int',
        'category'         => 'string',
        'address_id'       => 'int',
        'appointment_time' => 'string',
        'status'           => 'int'
    ];
}