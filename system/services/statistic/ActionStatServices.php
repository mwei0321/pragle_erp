<?php

/**
 * 动作每天统计查询
 * @Author: MaWei 
 * @Date: 2022-04-09 20:11:22 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-04-09 21:15:07
 */

namespace system\services\statistic;

use yii\db\Query;
use system\common\{TableMap, ServiceFactory};
use system\beans\statistic\ActionStatBeans;

class ActionStatServices
{
    /**
     * 返回某个时间段动作统计总值
     * @param  \system\beans\action\ActionStatBeans $statBeans
     * date: 2022-04-09 20:37:35
     * @author  <mawei.live>
     * @return void
     */
    function getActionStatisticListForDate(ActionStatBeans $statBeans)
    {
        // 字段
        $field = "department_id,staff_id,action_id,SUM(`value`) value";

        // 构建查询
        $query = (new Query())->from(TableMap::ActionDayStatisticsLog)
            ->where([
                "enterprise_id" => $statBeans->enterprise_id,
            ]);

        // 时间段
        if ($statBeans->stime && $statBeans->etime) {
            $query->andWhere([">=", "ctime", strtotime($statBeans->stime)])
                ->andWhere(["<=", "ctime", strtotime($statBeans->etime)]);
        }

        // 部门搜索
        if ($statBeans->department_id > 0) {
            $query->andWhere(["department_id" => $statBeans->department_id])
                ->groupBy("department_id,action_id");
        }

        // 员工搜索
        if ($statBeans->staff_id > 0) {
            $query->andWhere(["staff_id" => $statBeans->staff_id])
                ->groupBy("staff_id,action_id");
        }

        // 动作id
        if ($statBeans->action_id > 0) {
            $query->andWhere(["action_id" => $statBeans->action_id]);
        }

        // 总条数
        $count = $query->select("id")->count();
        if ($count < 1) {
            return [];
        }
        $statBeans->page($count);

        // 提示列表
        $query = $query->select($field)
            ->orderBy("staff_id DESC")
            ->limit($statBeans->limit)
            ->offset($statBeans->offset);
        $list = (new Query())->from(["a" => $query])
            ->select("a.*,g.name AS department,u.first_name,u.last_name")
            ->leftJoin(TableMap::Group . " as g", "g.id = a.department_id")
            ->leftJoin(TableMap::User . " as u", "u.id = a.staff_id")
            ->all();

        return $list;
    }
}
