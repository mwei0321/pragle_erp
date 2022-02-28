<?php
/* 动作KPI服务
 * @Author: MaWei 
 * @Date: 2022-01-06 10:35:44 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-01-23 21:42:25
 */

namespace system\services\graphic;

use yii\db\Query;
use system\common\{TableMap, ServiceFactory, HelperFuns};
use system\beans\kpi\KpiBeans;

class KpiGraphicServices
{



    /**
     * 个人销售条形图排行
     * @param  \system\beans\kpi\KpiBeans $kpiParams
     * date: 2022-01-23 20:08:21
     * @author  <mawei.live>
     * @return void
     */
    function getStaffMarketingBarChat(KpiBeans $kpiParams)
    {
        // 字段
        $field = 'staff_id,SUM(`target`) `target`,SUM(`completed`) `completed`';

        // 构建条件
        $query = (new Query())->select($field)
            ->from(TableMap::StaffMarketingKpi)
            ->where([
                'enterprise_id' => $kpiParams->enterprise_id,
                'year'          => $kpiParams->year,
            ]);

        // 员工
        if ($kpiParams->staff_id > 0) {
            $field = 'staff_id,GROUP_CONCAT(`target` ORDER BY `month` ASC) `target`,GROUP_CONCAT(`completed` ORDER BY `month` ASC) `completed`';
            $query->andWhere(["staff_id" => $kpiParams->staff_id]);
        } else {
            $query->orderBy("`target` DESC");
        }

        // 提取数据
        $query = $query->groupBy("staff_id");
        $list = (new Query())->from(["k" => $query])
            ->leftJoin(TableMap::User . ' AS u', 'u.id = k.staff_id')
            ->select("u.first_name,u.last_name,k.*")
            ->all();

        // 数据处理
        if ($list && $kpiParams->staff_id > 0) {
            foreach ($list as $k => $v) {
                $list[$k]["target"] = explode(",", $v["target"]);
                $list[$k]["completed"] = explode(",", $v["completed"]);
            }
        } else {
            return [];
        }

        return $list;
    }

    /**
     * 返回部门年总目标
     * @param  \system\beans\kpi\KpiBeans $kpiParams
     * date: 2022-02-28 23:51:47
     * @author  <mawei.live>
     * @return void
     */
    function getDepartmentMarketingBarChatForYear(KpiBeans $kpiParams)
    {
        // 字段
        $field = 'group_id,SUM(`target`) `target`,SUM(`completed`) `completed`';

        // 构建条件
        $query = (new Query())->select($field)
            ->from(TableMap::DepartmentMarketingKpi)
            ->where([
                'enterprise_id' => $kpiParams->enterprise_id,
                'year'          => $kpiParams->year,
            ]);

        // 部门
        if ($kpiParams->department_id > 0) {
            $query->andWhere(["department_id" => $kpiParams->department_id]);
        }

        // 提取数据
        $query = $query->groupBy("group_id");
        $list = (new Query())->from(["k" => $query])
            ->leftJoin(TableMap::Group . ' AS g', 'g.id = k.group_id')
            ->where(['>', 'target', 0])
            ->select("g.name,k.*")
            ->all();


        return $list;
    }

    /**
     * 部门销售条形图排行
     * @param  \system\beans\kpi\KpiBeans $kpiParams
     * date: 2022-01-23 20:07:19
     * @author  <mawei.live>
     * @return void
     */
    function getDepartmentMarketingBarChatForMonth(KpiBeans $kpiParams)
    {
        // 字段
        $field = 'group_id,GROUP_CONCAT(`target` ORDER BY `month` ASC) `target`,GROUP_CONCAT(`completed` ORDER BY `month` ASC) `completed`';

        // 构建条件
        $query = (new Query())->select($field)
            ->from(TableMap::DepartmentMarketingKpi)
            ->where([
                'enterprise_id' => $kpiParams->enterprise_id,
                'year'          => $kpiParams->year,
            ]);

        // 部门
        if ($kpiParams->department_id > 0) {
            $query->andWhere(["department_id" => $kpiParams->department_id]);
        }

        // 提取数据
        $query = $query->groupBy("group_id");
        $list = (new Query())->from(["k" => $query])
            ->leftJoin(TableMap::Group . ' AS g', 'g.id = k.group_id')
            ->select("g.name,k.*")
            ->all();

        // 数据处理
        if ($list) {
            foreach ($list as $k => $v) {
                $list[$k]["target"] = explode(",", $v["target"]);
                $list[$k]["completed"] = explode(",", $v["completed"]);
            }
        } else {
            return [];
        }

        return $list;
    }
}
