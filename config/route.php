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

    // ------------------ 车位相关接口 -------------------------------
    $group->post('carplace/list', 'Dolphin\Ting\Http\Service\CarPlaceService:getList');
    $group->post('carplace/detail', 'Dolphin\Ting\Http\Service\CarPlaceService:detail');
    $group->post('carplace/comment/list', 'Dolphin\Ting\Http\Service\CarPlaceService:commentList');

    // ------------------ 房屋相关接口 -------------------------------
    $group->post('house/list', 'Dolphin\Ting\Http\Service\HouseService:getList');
    $group->post('house/detail', 'Dolphin\Ting\Http\Service\HouseService:detail');

    // ------------------ 二手商品相关接口 -------------------------------
    $group->post('secondhand/detail', 'Dolphin\Ting\Http\Service\SecondhandGoodsService:detail');
    $group->post('secondhand/list', 'Dolphin\Ting\Http\Service\SecondhandGoodsService:getList');
    $group->post('secondhand/user/list', 'Dolphin\Ting\Http\Service\SecondhandGoodsService:getListByUid');

    // ------------------ 拼车相关接口 -----------------------------------
    $group->post('pinche/list', 'Dolphin\Ting\Http\Service\PincheService:getList');
    $group->post('pinche/detail', 'Dolphin\Ting\Http\Service\PincheService:detail');
    // ------------------ 租借相关接口 -----------------------------------
    $group->post('rent/list', 'Dolphin\Ting\Http\Service\RentService:getList');
    $group->post('rent/detail', 'Dolphin\Ting\Http\Service\RentService:detail');

    // ------------------ 回收相关接口 -----------------------------------
    $group->post('recycle/price/get', 'Dolphin\Ting\Http\Service\RecycleService:getPrice');

    // ------------------ 商品分类相关接口 -----------------------------------
    $group->post('product/category/lists', 'Dolphin\Ting\Http\Service\ProductService:getCategories');
    $group->post('product/index/list', 'Dolphin\Ting\Http\Service\ProductService:getProductList');

});

$app->group('/api/', function (RouteCollectorProxy $group) {
    // ------------------ 文件接口 ----------------------------------

    // ------------------ 圈子动态接口 -------------------------------
    $group->post('circle/add', 'Dolphin\Ting\Http\Service\CircleService:add');
    $group->post('circle/list', 'Dolphin\Ting\Http\Service\CircleService:getList');
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
    $group->post('carplace/user/list', 'Dolphin\Ting\Http\Service\CarPlaceService:getListByUid');

    // ------------------ 房屋相关接口 -------------------------------
    $group->post('house/add', 'Dolphin\Ting\Http\Service\HouseService:add');
    $group->post('house/user/list', 'Dolphin\Ting\Http\Service\HouseService:getListByUid');

    // ------------------ 二手商品相关接口 -------------------------------
    $group->post('secondhand/add', 'Dolphin\Ting\Http\Service\SecondhandGoodsService:add');

    // ------------------ 拼车相关接口 -----------------------------------
    $group->post('pinche/add', 'Dolphin\Ting\Http\Service\PincheService:add');
    $group->post('pinche/user/list', 'Dolphin\Ting\Http\Service\PincheService:getListByUid');

    // ------------------ 租借相关接口 -----------------------------------
    $group->post('rent/add', 'Dolphin\Ting\Http\Service\RentService:add');

    // ------------------ 地址相关接口 -----------------------------------
    $group->post('address/add', 'Dolphin\Ting\Http\Service\AddressService:add');
    $group->post('address/update', 'Dolphin\Ting\Http\Service\AddressService:update');
    $group->post('address/list', 'Dolphin\Ting\Http\Service\AddressService:getList');
    $group->post('address/delete', 'Dolphin\Ting\Http\Service\AddressService:delete');
    $group->post('address/default', 'Dolphin\Ting\Http\Service\AddressService:getDefaultAddress');

    // ------------------ 回收相关接口 -----------------------------------
    $group->post('recycle/add', 'Dolphin\Ting\Http\Service\RecycleService:add');
    $group->post('recycle/update', 'Dolphin\Ting\Http\Service\RecycleService:update');
    $group->post('recycle/status/update', 'Dolphin\Ting\Http\Service\RecycleService:updateStatus');
    $group->post('recycle/list', 'Dolphin\Ting\Http\Service\RecycleService:getList');
    $group->post('recycle/detail', 'Dolphin\Ting\Http\Service\RecycleService:detail');
    $group->post('recycle/delete', 'Dolphin\Ting\Http\Service\RecycleService:delete');

    // ------------------ 订单相关接口 -----------------------------------
    $group->post('order/list', 'Dolphin\Ting\Http\Service\OrderService:getOrderList');
    $group->post('order/detail', 'Dolphin\Ting\Http\Service\OrderService:getOrderDetail');

    // ------------------ 商品相关接口 -----------------------------------
    $group->post('product/category/list', 'Dolphin\Ting\Http\Service\ProductService:getCategoryList');
    $group->post('product/category/add', 'Dolphin\Ting\Http\Service\ProductService:addCategory');
    $group->post('product/category/update', 'Dolphin\Ting\Http\Service\ProductService:updateCategory');
    $group->post('product/category/delete', 'Dolphin\Ting\Http\Service\ProductService:deleteCategory');
    $group->post('product/add', 'Dolphin\Ting\Http\Service\ProductService:addProduct');
    $group->post('product/label/add', 'Dolphin\Ting\Http\Service\ProductService:addLabel');
    $group->post('product/label/list', 'Dolphin\Ting\Http\Service\ProductService:getLabelList');
    $group->post('product/label/delete', 'Dolphin\Ting\Http\Service\ProductService:deleteLabel');
    $group->post('product/material/add', 'Dolphin\Ting\Http\Service\ProductService:addMaterial');
    $group->post('product/material/update', 'Dolphin\Ting\Http\Service\ProductService:updateMaterial');
    $group->post('product/material/list', 'Dolphin\Ting\Http\Service\ProductService:getMaterialList');
    $group->post('product/material/delete', 'Dolphin\Ting\Http\Service\ProductService:deleteMaterial');
})->addMiddleware(new \Dolphin\Ting\Bootstrap\Middleware\AuthMiddleware($container));