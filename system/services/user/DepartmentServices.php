<?php
/*
 * @Author: MaWei 
 * @Date: 2022-04-06 09:32:07 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-04-06 11:43:03
 */

namespace system\services\user;

use yii\db\Query;
use system\common\{HelperFuns, TableMap, ServiceFactory};

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
    function getDepartmentTree($_enterpriseId)
    {
        $field = "id,enterprise_id,title,parent_id pid,front_state";
        $list = (new Query())->from(TableMap::Group)
            ->select($field)
            ->where([
                "enterprise_id" => $_enterpriseId,
                "type" => 2,
            ])
            ->orderBy("is_top DESC,is_bottom ASC")
            ->all();

        $list = HelperFuns::level($list);
        return HelperFuns::getTree($list, 3, 0);
    }
}
