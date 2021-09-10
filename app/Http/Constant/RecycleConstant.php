<?php
// 用户
namespace Dolphin\Ting\Http\Constant;

class RecycleConstant
{
    const APPOINTMENT = 1; // 预约
    const DOING = 2;  // 接单正在处理
    const FINISHED = 3;    // 完成
    const CANCEL = 4; // 取消
    const PRICES = [
        'paper' => [
            'tz'  => 0.5,
            'hz'  => 0.5,
            'chz' => 0.5,
            'zz'  => 0.5,
            'bz'  => 0.5
        ],
        'plastic' => 1.0,
        'metal' => [
            'tie' => 2.0,
            'tong' => 10.0,
            'lv'   => 3.0,
            'ylg'  => 4.0
        ],
        'dress' => 0.7,
        'hde' => 0.0
    ];
}