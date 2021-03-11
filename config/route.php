<?php
use Slim\Routing\RouteCollectorProxy;

$app->group('/api/', function (RouteCollectorProxy $group) {
    // ------------------ 文件接口 ----------------------------------
    $group->get('file/uploadtoken', 'Dolphin\Ting\Http\Service\FileService:getUploadToken');
    $group->post('file/delete', 'Dolphin\Ting\Http\Service\FileService:delete');

    // ------------------ 圈子动态接口 -------------------------------
    $group->post('circle/add', 'Dolphin\Ting\Http\Service\CircleService:add');
    $group->post('circle/list', 'Dolphin\Ting\Http\Service\CircleService:getList');
    $group->post('circle/comment', 'Dolphin\Ting\Http\Service\CircleService:comment');

    // ------------------ 车位相关接口 -------------------------------
    $group->post('carplace/add', 'Dolphin\Ting\Http\Service\CarPlaceService:add');
    $group->post('carplace/list', 'Dolphin\Ting\Http\Service\CarPlaceService:getList');
    $group->post('carplace/comment', 'Dolphin\Ting\Http\Service\CarPlaceService:comment');
});