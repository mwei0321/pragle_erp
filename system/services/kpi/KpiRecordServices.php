<?php

/* kpi record 服务处理类
 * @Author: MaWei 
 * @Date: 2022-01-17 23:22:51 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-01-17 23:44:37
 */

namespace system\services\kpi;

use yii\db\Query;
use system\common\{TableMap, ServiceFactory};
use system\beans\kpi\KpiBeans;

class KpiRecordServices
{

    /**
     * 返回kpi操作记录列表
     * @param  \system\beans\kpi\KpiBeans $kpiParams
     * date: 2022-01-17 23:40:09
     * @author  <mawei.live>
     * @return void
     */
    function getKpiRecordList(KpiBeans $kpiParams)
    {
        // 字段
        $field = "*";

        $query = (new Query())->select($field)
            ->from(TableMap::Follow);

        // 动作搜索 
        if ($kpiParams->action) {
            $query->andWhere(['state' => $kpiParams->action]);
        }

        // 用户搜索
        if ($kpiParams->staff_id) {
            $query->andWhere(['user_id' => $kpiParams->staff_id]);
        }

        // 总条数
        $count = $query->select('*')->count();
        if ($count < 1) {
            return [];
        }
        $kpiParams->page($count);

        // 提取记录
        $list = $query->orderBy("id DESC")
            ->limit($kpiParams->limit)
            ->offset($kpiParams->offset)
            ->all();

        return $list;
    }
}
