<?php
/* 动作KPI服务
 * @Author: MaWei 
 * @Date: 2022-01-06 10:35:44 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-01-23 21:42:25
 */

namespace system\services\graphic;

use yii\db\Query;
use system\common\{TableMap, ServiceFactory};
use system\beans\kpi\KpiBeans;

class KpiGraphicServices
{

    /**
     * 部门销售条形图排行
     * @param  \system\beans\kpi\KpiBeans $kpiParams
     * date: 2022-01-23 20:07:19
     * @author  <mawei.live>
     * @return void
     */
    function getDepartmentMarketingBarChat(KpiBeans $kpiParams)
    {
        // 字段
        $field = 'g.name,dm.department_id,SUM(dm.target) target,SUM(dm.completed) completed';

        // 构建条件
        $query = (new Query())->select($field)
            ->from(TableMap::DepartmentMarketingKpi . ' as dm')
            ->leftJoin(TableMap::Group . ' as g', "g.id = dm.department_id")
            ->where([
                'year' => $kpiParams->year,
            ]);

        // 提取数据
        $list = $query->orderBy("target DESC")
            ->groupBy("department_id")
            ->all();

        return $list;
    }

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
        $field = 'u.first_name,u.last_name,sm.staff_id,sm.month,sm.target,sm.completed';

        // 构建条件
        $query = (new Query())->select($field)
            ->from(TableMap::StaffMarketingKpi . ' AS sm')
            ->leftJoin(TableMap::User . ' AS u', 'u.id = sm.staff_id')
            ->where([
                'year'     => $kpiParams->year,
                'staff_id' => $kpiParams->staff_id,
            ]);

        // 提取数据
        $list = $query->orderBy("target DESC")
            ->all();

        return $list;
    }
}
