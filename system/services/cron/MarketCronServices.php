<?php

/*
 * 销售额定时任务统计
 * @Author: MaWei 
 * @Date: 2022-03-29 09:54:56 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-03-29 10:02:16
 */


namespace system\services\cron;

use yii\db\Query;
use system\common\{HelperFuns, TableMap, ServiceFactory};
use system\beans\kpi\ActionBeans;
use system\beans\cron\{CronMarketBeans};

class MarketCronServices
{

    /**
     * 销售额统计
     * date: 2022-03-29 09:58:07
     * @author  <mawei.live>
     * @return void
     */
    function actionMarketStatistics(CronMarketBeans $cronMarketBeans)
    {
        // 提取每天员工动作KPI
        $cronMarketBeans->year  = date('Y');

        // 按周期处理
        switch ($cronMarketBeans->cycle) {
            case 1: // 每天
                $cronMarketBeans->month = date('m');
                $cronMarketBeans->day   = date('d');
                break;
            case 2: // 每月
                $cronMarketBeans->month = date('m');
                break;
            case 3: // 每周
                $cronMarketBeans->week  = date('W');
                break;
            default:
                break;
        }

        // 提取员工动作KPI目标
        $list = ServiceFactory::getInstance("BaseDB", TableMap::StaffActionKpi)
            ->getListByCondition([
                "and",
                ["cycle"    => $cronMarketBeans->cycle],
                ["del_time" => 0],
                ["year"     => $cronMarketBeans->year],
                [">", "action_value", 0],
            ], "id,action_id,staff_id,enterprise_id,action_value target");
        if (!$list) {
            return -1;
        }
    }
}
