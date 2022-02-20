<?php

/* 动作完成日志
 * @Author: MaWei 
 * @Date: 2022-01-17 23:22:51 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-01-17 23:44:37
 */

namespace system\services\kpi;

use yii\db\Query;
use system\common\{TableMap, ServiceFactory};
use system\beans\kpi\ActionBeans;

class ActionLogServices
{

    /**
     * 返回指定条件的动作完成数
     * @param  \system\beans\kpi\ActionBeans $actionBeans
     * date: 2022-02-19 20:00:09
     * @author  <mawei.live>
     * @return void
     */
    function getAssginActionFinishNum(ActionBeans $actionBeans)
    {
        $query = (new Query())->from(TableMap::ActionDayStatisticsLog)
            ->select("SUM(`value`) cnt")
            ->where([
                "year" => $actionBeans->year,
            ]);

        // 月
        if ($actionBeans->month > 0) {
            $query->andWhere(["month" => $actionBeans->month]);
        }

        // 周
        if ($actionBeans->week > 0) {
            $query->andWhere(["week" => $actionBeans->week]);
        }

        // 日
        if ($actionBeans->day > 0) {
            $query->andWhere(["day" => $actionBeans->day]);
        }

        // 部门
        if ($actionBeans->department_id > 0) {
            $query->andWhere(["department_id" => $actionBeans->department_id]);
        }

        // 员工
        if ($actionBeans->staff_id > 0) {
            $query->andWhere(["staff_id" => $actionBeans->staff_id]);
        }

        // 动作
        if ($actionBeans->action_id > 0) {
            $query->andWhere(["action_id" => $actionBeans->action_id]);
        }

        // 提取记录
        $result = $query->one();

        return $result['cnt'] ?? 0;
    }
}
