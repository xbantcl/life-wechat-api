<?php
use Slim\Routing\RouteCollectorProxy;

$app->group('/api/', function (RouteCollectorProxy $group) {
    // ------------------ 登录接口 ----------------------------------
    $group->post('user/login', 'Dolphin\Ting\Http\Service\UserService:login');
    $group->post('user/register', 'Dolphin\Ting\Http\Service\UserService:register');
    $group->post('user/wx/login', 'Dolphin\Ting\Http\Service\UserService:wxLogin');

    // ------------------ 文件接口 ----------------------------------
    $group->get('file/uploadtoken', 'Dolphin\Ting\Http\Service\FileService:getUploadToken');
    $group->post('file/delete', 'Dolphin\Ting\Http\Service\FileService:delete');

    // ------------------ 圈子动态接口 -------------------------------
    $group->post('circle/list', 'Dolphin\Ting\Http\Service\CircleService:getList');

    // ------------------ 车位相关接口 -------------------------------
    $group->post('carplace/list', 'Dolphin\Ting\Http\Service\CarPlaceService:getList');
    $group->post('carplace/detail', 'Dolphin\Ting\Http\Service\CarPlaceService:detail');
    $group->post('carplace/comment/list', 'Dolphin\Ting\Http\Service\CarPlaceService:commentList');

    // ------------------ 二手商品相关接口 -------------------------------
    $group->post('secondhand/detail', 'Dolphin\Ting\Http\Service\SecondhandGoodsService:detail');
    $group->post('secondhand/list', 'Dolphin\Ting\Http\Service\SecondhandGoodsService:getList');
    $group->post('secondhand/user/list', 'Dolphin\Ting\Http\Service\SecondhandGoodsService:getListByUid');

    // ------------------ 拼车相关接口 -----------------------------------
    $group->post('pinche/list', 'Dolphin\Ting\Http\Service\PincheService:getList');
    $group->post('pinche/detail', 'Dolphin\Ting\Http\Service\PincheService:detail');
});

$app->group('/api/', function (RouteCollectorProxy $group) {
    // ------------------ 文件接口 ----------------------------------

    // ------------------ 圈子动态接口 -------------------------------
    $group->post('circle/add', 'Dolphin\Ting\Http\Service\CircleService:add');
    $group->post('circle/comment', 'Dolphin\Ting\Http\Service\CircleService:comment');
    $group->post('circle/user/list', 'Dolphin\Ting\Http\Service\CircleService:getListByUid');
    $group->post('circle/delete', 'Dolphin\Ting\Http\Service\CircleService:delete');
    $group->post('circle/comment/delete', 'Dolphin\Ting\Http\Service\CircleService:deleteComment');
    $group->post('circle/like', 'Dolphin\Ting\Http\Service\CircleService:like');
    $group->post('circle/unlike', 'Dolphin\Ting\Http\Service\CircleService:unlike');

    // ------------------ 车位相关接口 -------------------------------
    $group->post('carplace/add', 'Dolphin\Ting\Http\Service\CarPlaceService:add');
    $group->post('carplace/comment', 'Dolphin\Ting\Http\Service\CarPlaceService:comment');
    $group->post('carplace/comment/delete', 'Dolphin\Ting\Http\Service\CarPlaceService:deleteComment');

    // ------------------ 二手商品相关接口 -------------------------------
    $group->post('secondhand/add', 'Dolphin\Ting\Http\Service\SecondhandGoodsService:add');

    // ------------------ 拼车相关接口 -----------------------------------
    $group->post('pinche/add', 'Dolphin\Ting\Http\Service\PincheService:add');
    $group->post('pinche/user/list', 'Dolphin\Ting\Http\Service\PincheService:getListByUid');
})->addMiddleware(new \Dolphin\Ting\Bootstrap\Middleware\AuthMiddleware($container));