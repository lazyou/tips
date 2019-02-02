<?php

/**
 * 数组转树
 * @param $arr     源数组，所有数据都必须可以追溯到根节点 0，孤立的节点数据将被忽略
 * @param $confMap 配置项映射，可以指定输入数据中表示父子节点关系的字段 $confParentId 和 $confId，以及输出结果中表示子节点集合的属性名 $children
 * @return array
 */
function arrayToTree($arr, $confMap = array())
{
    // 配置字段: id parent_id children
    $confId = isset($confMap['id']) ? $confMap['id'] : 'id';
    $confParentId = isset($confMap['parent_id']) ? $confMap['parent_id'] : 'parent_id';
    $confChildrens = isset($confMap['childrens']) ? $confMap['childrens'] : 'childrens';

    // 首次遍历: 根据 parent_id 将一维数组分组为二维数组, 第一维度数组的 key 是 parent_id.
    $groupByParentId = function ($list) use ($confParentId) {
        $result[0] = [];

        foreach ($list as $item) {
            $parentId = $item[$confParentId];
            $result[$parentId][] = $item;
        }

        return $result;
    };

    /**
     * 闭包递归处理
     * &$list groupByParentId 处理后的二维数组; 此处引用传参传递是因为每个递归都要用到完整的 $list, $list 数据很大, 传地址省空间.
     * $parent 父级(首次从顶级第零级开始处理)
     * &$confId, &$confParentId, &$confChildrens 三个配置项对应的字符串
     */
    $buildTree = function (&$list, $parent) use (&$buildTree, &$confId, &$confParentId, &$confChildrens) {
        $tree = array();

        foreach ($parent as $key => $item) {
            $id = $item[$confId]; // 当前 id
            $item[$confChildrens] = [];

            // 当前 id 在 $list 种是否存在 其id值为key的数据
            if (isset($list[$id])) {
                $item[$confChildrens] = $buildTree($list, $list[$id]);
            }

            $tree[] = $item;
        }

        return $tree;
    };

    $groupArr = $groupByParentId($arr);
    $root = groupArr[0]; // 顶级数组: parent_id 为 0 的层级
    $result = $buildTree($groupArr, $root);

    return $result;
}

$arr = [
    [
        'id' => 1,
        'parent_id' => 0,
        'name' => '顶级1:蔬菜瓜果',
    ],
    [
        'id' => 2,
        'parent_id' => 0,
        'name' => '顶级2:数码设备',
    ],
    [
        'id' => 3,
        'parent_id' => 0,
        'name' => '顶级3',
    ],
    [
        'id' => 4,
        'parent_id' => 1,
        'name' => '萝卜',
    ],
    [
        'id' => 5,
        'parent_id' => 4,
        'name' => '白萝卜',
    ],
    [
        'id' => 6,
        'parent_id' => 4,
        'name' => '胡萝卜',
    ],
    [
        'id' => 7,
        'parent_id' => 1,
        'name' => '苹果',
    ],
    [
        'id' => 8,
        'parent_id' => 2,
        'name' => '华为',
    ],
    [
        'id' => 9,
        'parent_id' => 2,
        'name' => '小米',
    ],
    [
        'id' => 10,
        'parent_id' => 8,
        'name' => '华为nova',
    ],
];

print_r(arrayToTree($arr));
