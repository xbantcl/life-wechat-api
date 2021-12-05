<?php

namespace Dolphin\Ting\Http\Modules;

use Dolphin\Ting\Http\Exception\VegetableException;
use Dolphin\Ting\Http\Model\VegetableCategory;
use Dolphin\Ting\Http\Model\Vegetables;

class VegetableModule extends Module
{
    /**
     * 添加菜品
     *
     * @param $name
     * @param $price
     * @param $desc
     * @param $images
     * @return mixed
     *
     * @throws VegetableException
     */
    public function add($name, $price, $desc, $images)
    {
        try {
            $vegetable = Vegetables::select('id')->where('name', $name)->first();
            if ($vegetable instanceof Vegetables) {
                Vegetables::where('id', $vegetable->id)->update([
                    'name' => $name,
                    'price' => $price,
                    'desc' => $desc,
                    'images' => $images,
                ]);
                return $vegetable->id;
            }
            $vegetable = Vegetables::create([
                'name' => $name,
                'price' => $price,
                'desc' => $desc,
                'images' => $images,
            ]);
        } catch (VegetableException $e) {
            throw new VegetableException('VEGETABLE_DATA_ALREADY_EXIST');
        } catch (\Exception $e) {
            throw new VegetableException('ADD_VEGETABLE_DATA_ERROR');
        }
        return $vegetable->id;
    }

    /**
     * 更新菜品
     *
     * @param $id
     * @param $name
     * @param $price
     * @param $desc
     * @param $images
     *
     * @return bool
     *
     * @throws VegetableException
     */
    public function update($id, $name, $price, $desc, $images)
    {
        try {
            Vegetables::where('id', $id)->update([
                'name' => $name,
                'price' => $price,
                'desc' => $desc,
                'images' => $images
            ]);
        } catch (\Exception $e) {
            throw new VegetableException('UPDATE_VEGETABLE_DATA_ERROR');
        }
        return true;
    }

    /**
     * 获取菜品列表
     *
     * @param $categoryId
     * @param $start
     * @param $limit
     * @return array
     *
     * @throws VegetableException
     */
    public function getList($categoryId, $start = 0, $limit = 20)
    {
        try {
            $vegetableIds = [];
            if ($categoryId) {
                $data = VegetableCategory::select('vegetable_ids')->where('id', $categoryId)->first();
                $vegetableIds = explode(',' ,$data->vegetable_ids);
            }
            $query = Vegetables::select('id', 'name', 'price', 'desc');
            if ($vegetableIds) {
                $query->whereIn('id', $vegetableIds);
            }
            if ($start > 0) {
                $query->where('id', '>', $start);
            }
            $data = $query->take($limit + 1)->get()->toArray();
            $more = 0;
            if (empty($data)) {
                return ['start' => $start, 'more' => $more, 'list' => (object)[]];
            }
            if (count($data) > $limit) {
                $more = 1;
                array_pop($data);
            }
            $start = end($data)['id'];
            return ['start' => $start, 'more' => $more, 'list' => $data];
        } catch (\Exception $e) {
            throw new VegetableException('GET_VEGETABLES_LIST_ERROR');
        }
    }

    /**
     * 获取菜单名称列表
     *
     * @param int $start
     * @param int $limit
     * @return array
     * @throws VegetableException
     */
    public function getTagList($start = 0, $limit = 20)
    {
        try {
            $query = Vegetables::select('id', 'name');
            if ($start > 0) {
                $query->where('id', '>', $start);
            }
            $data = $query->take($limit + 1)->get()->toArray();
            $more = 0;
            if (empty($data)) {
                return ['start' => $start, 'more' => $more, 'list' => (object)[]];
            }
            if (count($data) > $limit) {
                $more = 1;
                array_pop($data);
            }
            $start = end($data)['id'];
            return ['start' => $start, 'more' => $more, 'list' => $data];
        } catch (\Exception $e) {
            throw new VegetableException('GET_VEGETABLES_LIST_ERROR');
        }
    }

    /**
     * 获取菜品信息
     *
     * @param $id
     * @return mixed
     *
     * @throws VegetableException
     */
    public function detail($id)
    {
        try {
            $data = Vegetables::select('id', 'name', 'price', 'desc', 'images')
                ->where('id', $id)
                ->first();
            return $data;
        } catch (\Exception $e) {
            throw new VegetableException('GET_VEGETABLES_DETAIL_ERROR');
        }
    }

    /**
     * 删除菜品
     *
     * @param $uid
     * @param $id
     * @return bool
     *
     * @throws VegetableException
     */
    public function delete($uid, $id)
    {
        try {
            Vegetables::where('id', $id)->delete();
            return true;
        } catch (\Exception $e) {
            throw new VegetableException('DELETE_VEGETABLES_ERROR');
        }
    }

    /**
     * 添加菜品分类
     *
     * @param $name
     * @param $vegetableIds
     * @return mixed
     * @throws VegetableException
     */
    public function addCategory($name, $vegetableIds)
    {
        try {
            $vegetable = VegetableCategory::where('name', $name)->first();
            if ($vegetable instanceof VegetableCategory) {
                VegetableCategory::where('id', $vegetable->id)->update(['vegetable_ids' => $vegetableIds]);
                return $vegetable->id;
            }
            $vegetable = VegetableCategory::create([
                'name' => $name,
                'vegetable_ids' => $vegetableIds
            ]);
        } catch (VegetableException $e) {
            throw new VegetableException('VEGETABLE_CATEGORY_DATA_ALREADY_EXIST');
        } catch (\Exception $e) {
            throw new VegetableException('ADD_VEGETABLE_CATEGORY_DATA_ERROR');
        }
        return $vegetable->id;
    }

    /**
     * 获取菜品分类列表
     *
     * @return mixed
     * @throws VegetableException
     */
    public function getCategoryList()
    {
        try {
            $data = VegetableCategory::select('id', 'name')->get()->toArray();
        } catch (\Exception $e) {
            throw new VegetableException('ADD_VEGETABLE_CATEGORY_DATA_ERROR');
        }
        return $data;
    }
}