<?php
/*
 * @Author: MaWei 
 * @Date: 2022-04-06 09:32:07 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-05-09 15:12:23
 */

namespace system\services\user;

use yii\db\Query;
use system\common\{HelperFuns, TableMap, ServiceFactory};
use system\beans\user\DepartmentBeans;

class DepartmentServices
{

    /**
     * 返回部门下的子部门
     * @param  [type] $_departmentId
     * date: 2022-04-06 11:42:09
     * @author  <mawei.live>
     * @return void
     */
    function getChildDepartmentById($_departmentId)
    {
        $field = "id,enterprise_id,title,parent_id pid,front_state";
        return (new Query())->from(TableMap::Group)
            ->select($field)
            ->where([
                "parent_id" => $_departmentId,
                "type" => 2,
            ])
            ->orderBy("is_top DESC,is_bottom ASC")
            ->all();
    }

    /**
     * 返回部门列表树
     * @param  int $_enterpriseId
     * date: 2022-04-06 09:37:19
     * @author  <mawei.live>
     * @return array
     */
    function getDepartmentList(DepartmentBeans $departmentBeans)
    {
        $cacheKey = "department_" . $departmentBeans->parent_id . '_' . $departmentBeans->front_state;
        // 缓存
        // $list = \Yii::$app->cache->get($cacheKey);
        // if ($list !== false) {
        //     return $list;
        // }

        // 字段
        $field = "id,name,parent_id,parent_id pid,front_state";

        // 构造查询
        $query = (new Query())->from(TableMap::Group)
            ->select($field)
            ->where([
                "enterprise_id" => $departmentBeans->enterprise_id,
                "type" => 2,
            ])
            ->orderBy("parent_id ASC,is_top DESC,is_bottom ASC");

        // 父级搜索
        if ($departmentBeans->parent_id > 0) {
            $query->andWhere(["parent_id" => $departmentBeans->parent_id]);
        }

        // 前台是否要展示，0否，1是
        if ($departmentBeans->front_state > 0) {
            $query->andWhere(['front_state' => $departmentBeans->front_state]);
        }

        // 提取结果
        $list = $query->all();

        // 加入层级
        $list = HelperFuns::level($list);

        // 写入缓存
        \Yii::$app->cache->set($cacheKey, $list, 180);


        return $list;
    }
}
