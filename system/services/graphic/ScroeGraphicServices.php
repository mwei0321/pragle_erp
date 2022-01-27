<?php

/* 积分条形图
 * @Author: MaWei 
 * @Date: 2022-01-23 21:52:07 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-01-23 22:09:56
 */


namespace system\services\graphic;

use yii\db\Query;
use system\common\{TableMap, ServiceFactory};
use system\beans\score\ScoreBeans;

class ScroeGraphicServices
{

    /**
     * 员工积分排行
     * @param  \system\beans\score\ScoreBeans $scoreParams
     * date: 2022-01-26 11:25:12
     * @author  <mawei.live>
     * @return void
     */
    function getStaffScore(ScoreBeans $scoreParams)
    {
        // 字段
        $field = "`obj_id` AS `staff_id`,SUM(`score`) AS `cnt`";

        // 构建
        $query = (new Query())->from(TableMap::DepartmentAndStaffScore)
            ->where([
                "enterprise_id" => $scoreParams->enterprise_id,
                "type"          => 1,
            ]);

        // 部门
        if ($scoreParams->department_id > 0) {
            // 提取部门下的人
            $staffId = (new Query())->from(TableMap::GroupMember)
                ->where([
                    'group_id' => $scoreParams->department_id,
                    "type"     => 2,
                ])->all();
            // 添加员工条件
            if ($staffId) {
                $staffId = array_column($staffId, 'target_id');
                $query->andWhere(['in', "obj_id", $staffId]);
            } else {
                return [];
            }
        }

        // 年
        if ($scoreParams->year) {
            $query->andWhere([
                'and',
                ['>=', 'day', $scoreParams->stime],
                ['<=', 'day', $scoreParams->etime],
            ]);
        }

        return $query->select($field)
            ->groupBy("staff_id")
            ->orderBy("score DESC")
            ->all();
    }

    /**
     * 部门积分排行
     * @param  \system\beans\score\ScoreBeans $scoreParams
     * date: 2022-01-23 22:02:58
     * @author  <mawei.live>
     * @return void
     */
    function getDepartmentScore(ScoreBeans $scoreParams)
    {
        // 字段
        $field = "`obj_id` AS `department_id`,SUM(`score`) AS `cnt`";

        // 构建
        $query = (new Query())->from(TableMap::DepartmentAndStaffScore)
            ->where([
                "enterprise_id" => $scoreParams->enterprise_id,
                "type"          => 2,
            ]);

        // 部门
        if ($scoreParams->department_id > 0) {
            // 提取部门下的人
            $departmentIds = (new Query())->from(TableMap::Group)
                ->select('id')
                ->where([
                    'parent_id' => $scoreParams->department_id,
                    "type"     => 2,
                ])->all();
            // 添加员工条件
            if ($departmentIds) {
                $departmentIds = array_column($departmentIds, 'id');
                $query->andWhere(['in', "obj_id", $departmentIds]);
            } else {
                return [];
            }
        }

        // 年
        if ($scoreParams->year) {
            $query->andWhere([
                'and',
                ['>', 'day', $scoreParams->stime],
                ['<', 'day', $scoreParams->etime],
            ]);
        }

        return $query->select($field)
            ->groupBy("staff_id")
            ->orderBy("score DESC")
            ->all();
    }
}
