<?php

namespace Dolphin\Ting\Http\Model;

use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'labels';

    public $guarded = ['id'];

    protected $casts = [
        'id'          => 'int',
        'category_id' => 'int',
        'name'        => 'string',
    ];
}