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
        'paper' => 0.5,
        'plastic' => 1.0,
        'metal' => 2.0,
        'dress' => 0.7,
        'hde' => 0.0
    ];
}