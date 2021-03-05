<?php
// 用户信息
namespace Dolphin\Ting\Http\Model;

use Illuminate\Database\Eloquent\Model;

class CircleComment extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'circle_comments';

    public $guarded = ['id'];

    protected $casts = [
        'id'          => 'int',
        'uid'         => 'int',
        'reply_uid'   => 'int',
        'post_id'     => 'int',
        'content'     => 'string'
    ];
}