<?php

namespace Dolphin\Ting\Http\Model;

use Illuminate\Database\Eloquent\Model;

class Vegetables extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'vegetables';

    public $guarded = ['id'];

    protected $casts = [
        'id'     => 'int',
        'uid'    => 'int',
        'name' => 'string',
    ];
}