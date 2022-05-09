<?php

/**
 * 动作每天统计查询
 * @Author: MaWei 
 * @Date: 2022-04-09 20:11:22 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-05-09 15:04:25
 */

namespace system\services\statistic;

use yii\db\Query;
use system\common\{HelperFuns, TableMap, ServiceFactory, SrvConfig};
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
        $field = "c.id AS action_id,c.parent_id,project_name name,group_id,state,ad.value";

        // 构建查询
        $query = (new Query())->from(TableMap::Config . ' AS c')
            ->where([
                'and',
                ['in', 'c.group_id', SrvConfig::$ActionGroup],
                // [">", "c.parent_id", 0]
            ]);

        // 总条数
        $count = $query->select("al.id")->count();
        if ($count < 1) {
            return [];
        }
        $statBeans->page($count);

        // 条件搜索
        $adQuery = (new Query())->from(TableMap::ActionDayStatisticsLog)
            ->where([
                "enterprise_id" => $statBeans->enterprise_id,
            ])
            ->select("action_id,SUM(`value`) value")
            ->groupBy("action_id");


        // 时间段
        if ($statBeans->stime && $statBeans->etime) {
            $adQuery->andWhere([">=", "ctime", strtotime($statBeans->stime)])
                ->andWhere(["<=", "ctime", strtotime($statBeans->etime)]);
        }

        // 部门搜索
        if ($statBeans->department_id > 0) {
            // 提取部门下级
            $departmentList = ServiceFactory::getInstance("DepartmentSrv")->getDepartmentList((new \system\beans\user\DepartmentBeans()));
            $departmentIds = $departmentList[$statBeans->department_id]['child_ids'] ?? [];
            $departmentIds[] = $statBeans->department_id;
            // 部门搜索
            $adQuery->andWhere(["IN", "department_id", $statBeans->department_id]);
            // ->groupBy("department_id,action_id");
        }

        // 员工搜索
        if ($statBeans->staff_id > 0) {
            $adQuery->andWhere(["staff_id" => $statBeans->staff_id]);
            // ->groupBy("staff_id,action_id");
        }

        // 动作id
        if ($statBeans->action_id > 0) {
            $adQuery->andWhere(["action_id" => $statBeans->action_id]);
        }

        // 提示列表
        $list = $query->select($field)
            ->leftJoin(["ad" => $adQuery], "ad.action_id = c.id")
            ->limit($statBeans->limit)
            ->offset($statBeans->offset)
            ->all();
        $list = $this->mergeGroup($list);

        return $list;
    }

    /**
     * 分类合并排序
     * @param  [type] $_list
     * date: 2022-05-09 14:46:30
     * @author  <mawei.live>
     * @return void
     */
    function mergeGroup($_list)
    {
        $mergeParentsIds = [231, 202];
        $list = HelperFuns::fieldtokey($_list, 'action_id');

        foreach ($list as $k => $v) {
            if ($v['parent_id'] == 0 && !in_array($v['parent_id'], $mergeParentsIds)) {
                unset($list[$k]);
            } elseif (in_array($v['parent_id'], $mergeParentsIds)) {
                $list[$v['parent_id']]['value'] = intval($v['value']);
            } else {
                $list[$k]['value'] = intval($v['value']);
            }
        }

        usort($list, (function ($a, $b) {
            if ($a['value'] > $b['value']) {
                return -1;
            } elseif ($a['value'] == $b['value']) {
                return 0;
            } elseif ($a['value'] < $b['value']) {
                return 1;
            }
        }));

        return $list;
    }
}
