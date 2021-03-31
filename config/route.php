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
    $group->post('secondhand/list', 'Dolphin\Ting\Http\Service\SecondhandService:getList');
});

$app->group('/api/', function (RouteCollectorProxy $group) {
    // ------------------ 文件接口 ----------------------------------

    // ------------------ 圈子动态接口 -------------------------------
    $group->post('circle/add', 'Dolphin\Ting\Http\Service\CircleService:add');
    $group->post('circle/comment', 'Dolphin\Ting\Http\Service\CircleService:comment');

    // ------------------ 车位相关接口 -------------------------------
    $group->post('carplace/add', 'Dolphin\Ting\Http\Service\CarPlaceService:add');
    $group->post('carplace/comment', 'Dolphin\Ting\Http\Service\CarPlaceService:comment');
    $group->post('carplace/comment/delete', 'Dolphin\Ting\Http\Service\CarPlaceService:deleteComment');

    // ------------------ 二手商品相关接口 -------------------------------
    $group->post('secondhand/add', 'Dolphin\Ting\Http\Service\SecondhandService:add');
})->addMiddleware(new \Dolphin\Ting\Bootstrap\Middleware\AuthMiddleware($container));