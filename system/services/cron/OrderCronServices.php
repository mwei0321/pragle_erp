<?php

/*
 * 跟进相关定时任务
 * @Author: MaWei 
 * @Date: 2022-03-18 20:24:15
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-03-18 20:24:15
 */

namespace system\services\cron;

use yii\db\Query;
use system\common\{HelperFuns, TableMap, ServiceFactory};
use system\beans\cron\CronActionBeans;

class OrderCronServices
{

    /**
     * 订单统计
     * date: 2022-03-18 20:24:15
     * @author  <mawei.live>
     * @return void
     */
    function getYesterdayMarket()
    {
        $cronActionBeans = new CronActionBeans();
        $time = date("Y-m-d", strtotime("-1 day"));
        $cronActionBeans->stime = strtotime($time);
        $cronActionBeans->etime = strtotime($time . " 23:59:59");

        // 实例化对象
        $srvObj = ServiceFactory::getInstance("BaseDB", TableMap::MarketDayStatisticsLog);

        // 员工订单统计数据
        $cronActionBeans->type = 1;
        $staffList = $this->getOrderStatisticsForType($cronActionBeans);
        HelperFuns::writeLog("Staff getYesterdayMarket count " . count($staffList), 'yestoday', 'getYesterdayMarket');

        // 提取员工部门
        $uesrIds = array_column($staffList, 'user_id');
        $department = (new Query())->select("id,department,enterprise_id")
            ->from(TableMap::User)
            ->where(["in", "id", $uesrIds])
            ->indexBy("id")
            ->all();

        // 日期处理
        list($year, $month, $day, $week) = [date("Y", $cronActionBeans->stime), date("m", $cronActionBeans->stime), date("d", $cronActionBeans->stime), date("W", $cronActionBeans->stime)];

        // 数据处理
        foreach ($staffList as $v) {
            if (intval($v["user_id"]) < 1) {
                continue;
            }
            $data                  = [];
            $data["enterprise_id"] = $department[$v["user_id"]]["enterprise_id"] ?? 0;
            $data["department_id"] = $department[$v["user_id"]]["department"] ?? 0;
            $data["staff_id"]      = $v['user_id'];
            $data["year"]          = $year;
            $data["month"]         = $month;
            $data["day"]           = $day;
            $data["week"]          = $week;
            $data["value"]         = $v['total_amount'];
            $data["ctime"]         = time();
            // 写入
            $result = $srvObj->insert($data);

            // 更新到销售计划
            $id = $srvObj->getFieldValByCondition([
                "enterprise_id" => $data["enterprise_id"],
                "staff_id"      => $data["staff_id"],
                "year"          => $data["year"],
                "month"         => $data["month"],
            ], "id", TableMap::StaffMarketingKpi);
            if (intval($id) > 1) {
                $srvObj->increment("completed", ["id" => $id], TableMap::StaffMarketingKpi, $data["value"], "utime => " . time());
            }

            // 写入订单个数动作统计 action_id=228
            $data["action_id"] = 228;
            $data["value"]     = $v["cnt"];
            $srvObj->insert($data, TableMap::ActionDayStatisticsLog);

            // 写入订单金额动作统计 action_id=227
            $data["action_id"] = 227;
            $data["value"]     = $v["total_amount"];
            $srvObj->insert($data, TableMap::ActionDayStatisticsLog);
        }

        return 1;
    }

    /**
     * 提取某个时段内的每个人的销售额
     * @param  \system\beans\cron\CronActionBeans $cronActionBeans
     * date: 2022-03-18 20:30:13
     * @author  <mawei.live>
     * @return array
     */
    function getOrderStatisticsForType(CronActionBeans $cronActionBeans)
    {
        // 时间过滤
        if (!($cronActionBeans->stime && $cronActionBeans->etime)) {
            return [];
        }

        // 字段
        $field = "`od`.`user_id`,SUM(`o`.`total_amount`) `total_amount`,COUNT(*) `cnt`";

        // 构建查询
        $query = (new Query())->from(TableMap::Order . ' AS `o`')
            ->leftJoin(TableMap::OrderDetail . ' AS `od`', '`od`.`order_num` = `o`.`order_num`')
            ->where([
                "and",
                ["o.is_delete" => 0],
                [">", "o.status", 0],
                [">=", "o.created_at", $cronActionBeans->stime],
                ["<=", "o.created_at", $cronActionBeans->etime],
            ]);

        return $query->select($field)
            ->groupBy("`od`.`user_id`")
            ->all();
    }
}
