<?php

/*
 * 动作执行
 * @Author: MaWei 
 * @Date: 2022-02-19 19:45:56 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-04-09 20:48:48
 */

namespace system\services\cron;

use yii\db\Query;
use system\common\{HelperFuns, TableMap, ServiceFactory};
use system\beans\cron\CronActionBeans;

class CustomerCronServices
{
    /**
     * 获取昨天新增客户统计
     * date: 2022-02-19 12:04:59
     * @author  <mawei.live>
     * @return void
     */
    function getYesterdayCustomerStatistics($_date = null)
    {
        $cronActionBeans = new CronActionBeans();
        $time = $_date ? $_date : date("Y-m-d", strtotime("-1 day"));
        $cronActionBeans->stime = strtotime($time);
        $cronActionBeans->etime = strtotime($time . " 23:59:59");

        // 提取统计数据
        $list = $this->getCustomerStatistics($cronActionBeans);
        HelperFuns::writeLog("Customer YesterdayCustomerStatistics count " . count($list), '/yestoday', 'YesterdayCustomerStatistics');

        // 提取分组
        $uesrIds = array_column($list, 'user_id');
        $department = (new Query())->select("id,department ")
            ->from(TableMap::User)
            ->where(["in", "id", $uesrIds])->indexBy("id")->all();

        // 日期处理
        $time = $cronActionBeans->stime + 3600;
        list($year, $month, $day, $week) = [date("Y", $time), date("m", $time), date("d", $time), date("W", $time)];

        // 实例化对象
        $srvObj = ServiceFactory::getInstance("BaseDB", TableMap::ActionDayStatisticsLog);

        // 数据处理
        foreach ($list as $v) {
            $data["enterprise_id"] = $v['enterprise_id'];
            $data["department_id"] = $department[$v["user_id"]]["department"] ?? 0;
            $data["staff_id"]      = $v['user_id'];
            $data["year"]          = $year;
            $data["month"]         = $month;
            $data["day"]           = $day;
            $data["week"]          = $week;
            $data["value"]         = $v['cnt'];
            $data["action_id"]     = 222;
            $data["ctime"]         = $time;
            // 写入
            $result = $srvObj->insert($data);
        }

        return 1;
    }


    /**
     * 返回新增客户的统计
     * @param  \system\beans\cron\CronActionBeans $cronActionBeans
     * @date: 2022-02-15 18:33:20.
     * @author  <mawei.live>
     * @return array
     */
    function getCustomerStatistics(CronActionBeans $cronActionBeans)
    {
        // 时间过滤
        if (!($cronActionBeans->stime)) {
            return [];
        }

        // 字段
        $field = "`id` `enterprise_id`,`belong_to` `user_id`,COUNT(*) `cnt`";

        // 构建查询
        $query = (new Query())->from(TableMap::Enterprise)
            ->where([
                "and",
                ['>=', 'created_at', $cronActionBeans->stime],
                ['<=', 'created_at', $cronActionBeans->etime],
            ]);

        // 用户
        if ($cronActionBeans->staff_id) {
            $query->andWhere(["user_id" => $cronActionBeans->staff_id]);
        }

        return $query->select($field)
            ->groupBy("belong_to")
            ->all();
    }
}
