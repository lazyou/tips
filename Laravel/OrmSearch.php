<?php
/**
 * usage:
 *   $account = getRequest('like.account'); // 账号
 *   $dealerName = getRequest('like.dealer_name'); // 所属经销商
 *
 *   $departments = search(new Department(), $fieldMaps)
 *       ->with([
 *           'dealer',
 *           'province',
 *           'city',
 *           'district',
 *       ])
 *       ->when($account, function ($query) use ($account) {
 *           return $query->whereIn('department_user_id', User::getIdsByLike($account));
 *       })
 *       ->when($dealerName, function ($query) use ($dealerName) {
 *           return $query->whereIn('dealer_id', Dealer::getIdsByLike($dealerName));
 *       })
 *       ->forDepartment()
 *       ->Paging();
 */
if (!function_exists('search')) {
    /**
     * 通用模型搜索
     *
     * @param  [type] $model        [description]
     * @param  [type] $fieldMaps [description]
     * @return [type]               [description]
     */
    function search($model, $fieldMaps)
    {
        $params = request()->all();

        $searchFields = array_keys($fieldMaps);

        foreach ($params as $key => $fields) {
            if (!is_array($fields)) {
                continue;
            }

            foreach ($fields as $field => $value) {
                // 不在搜索配置里的字段不参与 sql 条件的拼接
                if (!in_array($field, $searchFields)) {
                    continue;
                }

                $trueField = $fieldMaps[$field];

                switch ($key) {
                    case 'like' :
                        $model = $model->where($trueField, 'like', "%{$value}%");
                        break;
                    case 'equal' :
                        $model = $model->where($trueField, "{$value}");
                        break;
                    case 'in' :
                        $model = $model->whereIn($trueField, $value);
                        break;
                    case 'between' :
                        $model = $model->where($trueField, '>=', $value[0]);
                        $model = $model->where($trueField, '<=', $value[1]);
                        break;
                    case 'order' :
                        $model = $model->orderBy($trueField, $value);
                        break;
                }
            }
        }

        return $model;
    }
}
