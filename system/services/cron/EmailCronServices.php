<?php

/*
 * 邮件相关定时任务
 * @Author: MaWei 
 * @Date: 2022-02-13 20:20:18 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-02-24 23:13:54
 */

namespace system\services\cron;

use yii\db\Query;
use system\common\{HelperFuns, TableMap, ServiceFactory};
use system\beans\cron\CronActionBeans;

class EmailCronServices
{

    /**
     * 获取昨天邮件发送统计
     * date: 2022-02-19 12:04:59
     * @author  <mawei.live>
     * @return void
     */
    function getYesterdayEmailStatistics()
    {
        $cronActionBeans = new CronActionBeans();
        $time = date("Y-m-d", strtotime("-1 day"));
        // $time = date("Y-m-d", '1645204141');
        $cronActionBeans->stime = strtotime($time);
        $cronActionBeans->etime = strtotime($time . " 23:59:59");

        // 提取统计数据
        $list = $this->getEmailStatistics($cronActionBeans);

        // 提取分组
        $uesrIds = array_column($list, 'user_id');
        $department = (new Query())->select("id,user_id")
            ->from(TableMap::Group)
            ->where([
                "and",
                ["type" => 2],
                ["in", "user_id", $uesrIds],
            ])->indexBy("user_id")->all();

        // 实例化对象
        $srvObj = ServiceFactory::getInstance("BaseDB", TableMap::ActionDayStatisticsLog);
        // 数据处理
        foreach ($list as $v) {
            $data["enterprise_id"] = $v['enterprise_id'];
            $data["department_id"] = $department[$v["user_id"]]["id"] ?? 0;
            $data["staff_id"]      = $v['user_id'];
            $data["year"]          = date("Y", $cronActionBeans->stime + 3600);
            $data["month"]         = date("m", $cronActionBeans->stime + 3600);
            $data["day"]           = date("d", $cronActionBeans->stime + 3600);
            $data["week"]          = date("W", $cronActionBeans->stime + 3600);
            $data["value"]         = $v['cnt'];
            $data["action_id"]     = 225;
            $data["ctime"]         = time();
            // 写入
            $result = $srvObj->insert($data);
        }

        return 1;
    }

    /**
     * 返回邮件的统计
     * @param  \system\beans\cron\CronActionBeans $cronActionBeans
     * @date: 2022-02-15 18:33:20.
     * @author  <mawei.live>
     * @return array
     */
    function getEmailStatistics(CronActionBeans $cronActionBeans)
    {
        // 时间过滤
        if (!($cronActionBeans->stime && $cronActionBeans->etime)) {
            return [];
        }

        // 字段
        $field = "tq.enterprise_id,tq.user_id,COUNT(*) cnt";

        // 构建查询
        $query = (new Query())->from(TableMap::TaskQueue . " AS tq")
            ->leftJoin(TableMap::TaskDistribute . " AS td", "td.task_id = tq.id")
            ->where([
                "and",
                ["tq.type" => 1],
                ["in", "td.state", [1, 2, 3]],
                [">=", "tq.created_at", $cronActionBeans->stime],
                ["<=", "tq.created_at", $cronActionBeans->etime],
            ]);

        // 用户
        if ($cronActionBeans->staff_id) {
            $query->andWhere(["tq.user_id" => $cronActionBeans->staff_id]);
        }

        return $query->select($field)
            ->groupBy("tq.user_id")
            ->all(\Yii::$app->dbdata);
    }
}
