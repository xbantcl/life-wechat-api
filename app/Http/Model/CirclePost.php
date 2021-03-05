<?php
// 用户信息
namespace Dolphin\Ting\Http\Model;

use Illuminate\Database\Eloquent\Model;

class CirclePost extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'circle_post';

    public $guarded = ['id'];

    protected $casts = [
        'id'          => 'int',
        'uid'         => 'int',
        'content'     => 'string',
        'images'      => 'string'
    ];
}