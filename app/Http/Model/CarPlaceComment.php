<?php
// 用户信息
namespace Dolphin\Ting\Http\Model;

use Illuminate\Database\Eloquent\Model;

class CarPlaceComment extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'car_place_comments';

    public $guarded = ['id'];

    protected $casts = [
        'id'           => 'int',
        'uid'          => 'int',
        'car_place_id' => 'int',
        'reply_uid'    => 'int',
        'content'      => 'string'
    ];
}