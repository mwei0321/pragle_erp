<?php

/* 积分条形图
 * @Author: MaWei 
 * @Date: 2022-01-23 21:52:07 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-01-23 22:09:56
 */


namespace system\services\graphic;

use yii\db\Query;
use system\common\{HelperFuns, TableMap, ServiceFactory};
use system\beans\score\ScoreBeans;

class ScroeGraphicServices
{

    /**
     * 返回指定年的12月统计
     * @param  \system\beans\score\ScoreBeans $scoreParams
     * date: 2022-02-11 17:27:21
     * @author  <mawei.live>
     * @return void
     */
    function getStaffMonthScore(ScoreBeans $scoreParams)
    {
        // 字段
        $field = "`staff_id`,`month`,SUM(`ds`.`score`) AS `cnt`,first_name,last_name";

        // 提取条件构建
        $query = $this->_staffScoreQuery($scoreParams);
        if (!$query) {
            return [];
        }

        // 提取结果 
        $result = $query->select($field)
            ->groupBy("staff_id,month")
            ->orderBy("cnt DESC")
            ->all();
        if (!$result) {
            return [];
        }

        // 数据处理
        $result = HelperFuns::classifyMergeArray($result, "staff_id");
        $month = ["01" => "0", "02" => "0", "03" => "0", "04" => "0", "05" => "0", "06" => "0", "07" => "0", "08" => "0", "09" => "0", "10" => "0", "11" => "0", "12" => "0"];

        // 实例化对象
        $userObj = ServiceFactory::getInstance("BaseDB", TableMap::User);

        // 月份补全
        $data = [];
        foreach ($result as $k => $v) {
            $exsit                = array_combine(array_column($v, "month"), array_column($v, "cnt"));
            $score                = array_merge($month, $exsit);
            // $tmp = $userObj->getInfoById($v[0]["staff_id"], "first_name,last_name");
            $data[$k]['name']     = ($v[0]["first_name"] ?? "") . ' ' . ($v[0]["last_name"] ?? "");
            $data[$k]['staff_id'] = $v[0]["staff_id"];
            $data[$k]['count']    = $score ? array_values($score) : ["0", "0", "0", "0", "0", "0", "0", "0", "0", "0", "0", "0"];
        }

        return array_values($data);
    }

    /**
     * 员工积分排行
     * @param  \system\beans\score\ScoreBeans $scoreParams
     * date: 2022-01-26 11:25:12
     * @author  <mawei.live>
     * @return void
     */
    function getStaffYearScore(ScoreBeans $scoreParams)
    {
        // 字段
        $field = "`staff_id`,SUM(`score`) AS `cnt`";

        // 提取条件构建
        $query = $this->_staffScoreQuery($scoreParams);

        // 提取数据
        return $query->select($field)
            ->groupBy("staff_id")
            ->orderBy("score DESC")
            ->all();
    }

    /**
     * 构建员工积分查询条件query
     * @param  \system\beans\score\ScoreBeans $scoreParams
     * date: 2022-02-11 17:33:21
     * @author  <mawei.live>
     * @return object
     */
    private function _staffScoreQuery(ScoreBeans $scoreParams)
    {
        // 构建
        $query = (new Query())->from(TableMap::DepartmentAndStaffScore . ' AS ds')
            ->leftJoin(TableMap::User . " AS u", "u.id = ds.staff_id")
            ->where([
                "ds.enterprise_id" => $scoreParams->enterprise_id,
                "ds.type"          => 1,
                "u.state"          => 1,
            ]);

        // 部门
        if ($scoreParams->department_id > 0) {
            $query->andWhere([
                'department' => $scoreParams->department_id,
            ]);
            // // 提取部门下的人
            // $staffId = (new Query())->from(TableMap::User)
            //     ->where([
            //         'department' => $scoreParams->department_id,
            //         "state"     => 1,
            //     ])->all();
            // // 添加员工条件
            // if ($staffId) {
            //     $staffId = array_column($staffId, 'target_id');
            //     $query->andWhere(['in', "obj_id", $staffId]);
            // } else {
            //     return null;
            // }
        }

        // 年
        if ($scoreParams->year) {
            $query->andWhere([
                "year" => $scoreParams->year
            ]);
        }

        return $query;
    }


    //------->>>>>>>------部门相关积分------<<<<<<<&&&&>>>>>>---MaWei@2022-02-11 18:09----<<<<<<----//

    /**
     * 构建部门积分查询条件query
     * @param  \system\beans\score\ScoreBeans $scoreParams
     * date: 2022-01-23 22:02:58
     * @author  <mawei.live>
     * @return void
     */
    function getDepartmentMonthScore(ScoreBeans $scoreParams)
    {
        // 字段
        $field = "`department_id`,`month`,SUM(`score`) AS `cnt`";

        // 提取条件构建
        $query = $this->_departmentScoreQuery($scoreParams);
        if (!$query) {
            return [];
        }

        // 提取结果 
        $result = $query->select($field)
            ->groupBy("department_id,month")
            ->orderBy("score DESC")
            ->all();
        if (!$result) {
            return [];
        }

        // 数据处理
        $result = HelperFuns::classifyMergeArray($result, "department_id");
        $month = ["01" => "0", "02" => "0", "03" => "0", "04" => "0", "05" => "0", "06" => "0", "07" => "0", "08" => "0", "09" => "0", "10" => "0", "11" => "0", "12" => "0"];
        // 实例化对象
        $userObj = ServiceFactory::getInstance("BaseDB", TableMap::Group);

        // 月份补全
        $data = [];
        foreach ($result as $k => $v) {
            $exsit                     = array_combine(array_column($v, "month"), array_column($v, "cnt"));
            $score                     = array_merge($month, $exsit);
            $data[$k]['name']          = $userObj->getFieldValById($v[0]["department_id"]);
            $data[$k]['department_id'] = $v[0]["department_id"];
            $data[$k]['count']         = $score ? array_values($score) : ["0", "0", "0", "0", "0", "0", "0", "0", "0", "0", "0", "0"];
        }

        return array_values($data);
    }

    /**
     * Undocumented function
     * @param  \system\beans\score\ScoreBeans $scoreParams
     * date: 2022-02-11 18:11:52
     * @author  <mawei.live>
     * @return object
     */
    private function _departmentScoreQuery(ScoreBeans $scoreParams)
    {
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
                return null;
            }
        }

        // 年
        if ($scoreParams->year) {
            $query->andWhere([
                "year" => $scoreParams->year
            ]);
        }

        return $query;
    }
}
