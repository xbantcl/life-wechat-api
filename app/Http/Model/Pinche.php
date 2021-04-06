<?php

namespace Dolphin\Ting\Http\Model;

use Illuminate\Database\Eloquent\Model;

class Pinche extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pinche';

    public $guarded = ['id'];

    protected $casts = [
        'id'          => 'int',
        'uid'         => 'int'
    ];
}