<?php
// 用户信息
namespace Dolphin\Ting\Http\Model;

use Illuminate\Database\Eloquent\Model;

class Circle extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'circle';

    public $guarded = ['id'];

    protected $casts = [
        'id'          => 'int',
        'uid'         => 'int',
        'content'     => 'string',
        'images'      => 'string',
        'create_time' => 'int',
        'modify_time' => 'int'
    ];
}