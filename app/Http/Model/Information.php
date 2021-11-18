<?php

namespace Dolphin\Ting\Http\Model;

use Illuminate\Database\Eloquent\Model;

class Information extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'informations';

    public $guarded = ['id'];

    protected $casts = [
        'id'          => 'int',
        'uid'         => 'int',
    ];
}