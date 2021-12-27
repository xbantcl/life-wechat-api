<?php
use Slim\Routing\RouteCollectorProxy;

$app->group('/api/', function (RouteCollectorProxy $group) {
    // ------------------ 登录接口 ----------------------------------
    $group->post('user/login', 'Dolphin\Ting\Http\Service\UserService:login');
    $group->post('user/register', 'Dolphin\Ting\Http\Service\UserService:register');
    $group->post('user/wx/login', 'Dolphin\Ting\Http\Service\UserService:wxLogin');
    $group->post('user/wx/access_token', 'Dolphin\Ting\Http\Service\UserService:getAccessToken');

    // ------------------ 文件接口 ----------------------------------
    $group->get('file/uploadtoken', 'Dolphin\Ting\Http\Service\FileService:getUploadToken');
    $group->post('file/delete', 'Dolphin\Ting\Http\Service\FileService:delete');

    // ------------------ 圈子动态接口 -------------------------------
    $group->post('circle/list', 'Dolphin\Ting\Http\Service\CircleService:getList');

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

    // ------------------ 买菜相关接口 -----------------------------------
    $group->post('vegetable/detail', 'Dolphin\Ting\Http\Service\VegetableService:detail');
    $group->post('vegetable/list', 'Dolphin\Ting\Http\Service\VegetableService:getList');
    $group->post('vegetable/tag/list', 'Dolphin\Ting\Http\Service\VegetableService:getTagList');
    $group->post('vegetable/category/list', 'Dolphin\Ting\Http\Service\VegetableService:getCategoryList');

    // ------------------ 动态信息相关接口 -----------------------------------
    $group->post('information/list', 'Dolphin\Ting\Http\Service\InformationService:getList');
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
    $group->post('circle/status/change', 'Dolphin\Ting\Http\Service\CircleService:changeCircleStatus');
    // ------------------ 车位相关接口 -------------------------------
    $group->post('carplace/add', 'Dolphin\Ting\Http\Service\CarPlaceService:add');
    $group->post('carplace/comment', 'Dolphin\Ting\Http\Service\CarPlaceService:comment');
    $group->post('carplace/comment/delete', 'Dolphin\Ting\Http\Service\CarPlaceService:deleteComment');
    $group->post('carplace/user/list', 'Dolphin\Ting\Http\Service\CarPlaceService:getListByUid');
    $group->post('carplace/status/change', 'Dolphin\Ting\Http\Service\CarPlaceService:changeStatus');
    $group->post('carplace/delete', 'Dolphin\Ting\Http\Service\CarPlaceService:delete');
    $group->post('carplace/image/update', 'Dolphin\Ting\Http\Service\CarPlaceService:delete');

    // ------------------ 房屋相关接口 -------------------------------
    $group->post('house/add', 'Dolphin\Ting\Http\Service\HouseService:add');
    $group->post('house/user/list', 'Dolphin\Ting\Http\Service\HouseService:getListByUid');
    $group->post('house/status/change', 'Dolphin\Ting\Http\Service\HouseService:changeStatus');
    $group->post('house/delete', 'Dolphin\Ting\Http\Service\HouseService:delete');

    // ------------------ 二手商品相关接口 -------------------------------
    $group->post('secondhand/add', 'Dolphin\Ting\Http\Service\SecondhandGoodsService:add');
    $group->post('secondhand/user/list', 'Dolphin\Ting\Http\Service\SecondhandGoodsService:getListByUid');
    $group->post('secondhand/delete', 'Dolphin\Ting\Http\Service\SecondhandGoodsService:delete');
    $group->post('secondhand/status/change', 'Dolphin\Ting\Http\Service\SecondhandGoodsService:changeStatus');

    // ------------------ 拼车相关接口 -----------------------------------
    $group->post('pinche/add', 'Dolphin\Ting\Http\Service\PincheService:add');
    $group->post('pinche/user/list', 'Dolphin\Ting\Http\Service\PincheService:getListByUid');

    // ------------------ 租借相关接口 -----------------------------------
    $group->post('rent/add', 'Dolphin\Ting\Http\Service\RentService:add');
    $group->post('rent/delete', 'Dolphin\Ting\Http\Service\RentService:delete');
    $group->post('rent/status/change', 'Dolphin\Ting\Http\Service\RentService:changeStatus');

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

    // ------------------ 快递订单相关接口 -----------------------------------
    $group->post('delivery/order/add', 'Dolphin\Ting\Http\Service\DeliveryOrderService:add');
    $group->post('delivery/order/list', 'Dolphin\Ting\Http\Service\DeliveryOrderService:getList');
    $group->post('delivery/order/detail', 'Dolphin\Ting\Http\Service\DeliveryOrderService:detail');
    $group->post('delivery/order/status/change', 'Dolphin\Ting\Http\Service\DeliveryOrderService:changeStatus');

    // ------------------ 买菜相关接口 -----------------------------------
    $group->post('vegetable/add', 'Dolphin\Ting\Http\Service\VegetableService:add');
    $group->post('vegetable/update', 'Dolphin\Ting\Http\Service\VegetableService:update');
    $group->post('vegetable/delete', 'Dolphin\Ting\Http\Service\VegetableService:delete');
    $group->post('vegetable/category/add', 'Dolphin\Ting\Http\Service\VegetableService:addCategory');
    $group->post('vegetable/price', 'Dolphin\Ting\Http\Service\VegetableService:getVegetablesPrice');
    $group->post('vegetable/order/add', 'Dolphin\Ting\Http\Service\VegetableOrderService:add');
    $group->post('vegetable/order/update', 'Dolphin\Ting\Http\Service\VegetableOrderService:update');
    $group->post('vegetable/order/list', 'Dolphin\Ting\Http\Service\VegetableOrderService:getList');
    $group->post('vegetable/order/detail', 'Dolphin\Ting\Http\Service\VegetableOrderService:detail');
    $group->post('vegetable/order/delete', 'Dolphin\Ting\Http\Service\VegetableOrderService:delete');

    // ------------------ 动态信息相关接口 -----------------------------------
    $group->post('information/add', 'Dolphin\Ting\Http\Service\InformationService:add');
    $group->post('information/delete', 'Dolphin\Ting\Http\Service\InformationService:delete');
    $group->post('information/status/change', 'Dolphin\Ting\Http\Service\InformationService:changeStatus');
    $group->post('information/user/list', 'Dolphin\Ting\Http\Service\InformationService:getList');
})->addMiddleware(new \Dolphin\Ting\Bootstrap\Middleware\AuthMiddleware($container));