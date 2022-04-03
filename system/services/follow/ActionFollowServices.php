<?php

/* 动作跟进服务
* @Author: MaWei
* @Date: 2022-01-06 10:35:44
* @Last Modified by: MaWei
* @Last Modified time: 2022-01-17 23:06:01
*/

namespace system\services\follow;

use yii\db\Query;
use system\common\{TableMap, ServiceFactory};
use system\beans\follow\FollowBeans;

class ActionFollowServices
{

    /**
     * 动作跟进列表
     * @param  \system\beans\follow\FollowBeans $followParams
     * date: 2022-01-22 23:45:15
     * @author  <mawei.live>
     * @return void
     */
    function getActionFollowList(FollowBeans $followParams)
    {
        // 字段
        $field = '*';

        // 构建条件
        $query = (new Query())->select($field)
            ->from(TableMap::ActionFollow);

        // 动作
        if ($followParams->action_id) {
            $followParams->action_id = explode(',', $followParams->action_id);
            $query->andWhere(['in', 'action_id', $followParams->action_id]);
        }

        // 企业ID
        if ($followParams->enterprise_id > 0) {
            $query->andWhere(["enterprise_id" => $followParams->enterprise_id]);
        }

        // 个人
        if ($followParams->staff_id > 0) {
            $query->andWhere(["staff_id" => $followParams->staff_id]);
        }

        // 部门
        if ($followParams->department_id > 0) {
            $query->andWhere(["department_id" => $followParams->department_id]);
        }

        // 类型
        if ($followParams->type > 0) {
            $query->andWhere(["type" => $followParams->type]);
        }

        // 总条数
        $count = $query->count();
        if ($count < 1) {
            return [];
        }
        // 分页 
        $followParams->page($count);

        // 排序提取
        $list = $query->orderBy("utime DESC")
            ->limit($followParams->limit)
            ->offset($followParams->offset)
            ->all();

        // 是否格式化时间
        if (isset($followParams->timeFormat)) {
            foreach ($list as $k => $v) {
                $list[$k]["utime"] = $v["utime"] ? date($followParams->timeFormat, $v["utime"]) : "";
                $list[$k]["ctime"] = date($followParams->timeFormat, $v["ctime"]);
            }
        }

        return $list;
    }
}
