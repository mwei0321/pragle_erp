<?php

/*
 * 跟进相关定时任务
 * @Author: MaWei 
 * @Date: 2022-02-13 20:20:18 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-04-09 20:48:25
 */

namespace system\services\cron;

use yii\db\Query;
use system\common\{HelperFuns, TableMap, ServiceFactory};
use system\beans\cron\CronActionBeans;

class FollowCronServices
{

    /**
     * 获取昨天跟进统计
     * date: 2022-02-19 12:04:59
     * @author  <mawei.live>
     * @return void
     */
    function getYesterdayActionFollow($_date = "")
    {
        $cronActionBeans = new CronActionBeans();
        $time = $_date ? strtotime($_date) : date("Y-m-d", strtotime("-1 day"));
        // $time = "2022-03-03";
        $cronActionBeans->stime = strtotime($time);
        $cronActionBeans->etime = strtotime($time . " 23:59:59");

        // 实例化对象
        $srvObj = ServiceFactory::getInstance("BaseDB", TableMap::ActionDayStatisticsLog);

        // 员工跟进统计数据
        $cronActionBeans->type = 1;
        $staffList = $this->getActionFollowStatisticsForType($cronActionBeans);
        HelperFuns::writeLog("Staff YesterdayActionFollow count " . count($staffList), 'yestoday', 'YesterdayActionFollow');


        // 提取员工部门
        $uesrIds = array_column($staffList, 'staff_id');
        $department = (new Query())->select("id,department ")
            ->from(TableMap::User)
            ->where(["in", "id", $uesrIds])->indexBy("id")->all();

        // 数据处理
        foreach ($staffList as $v) {
            $data["enterprise_id"] = $v['enterprise_id'];
            $data["department_id"] = $department[$v["staff_id"]]["department"] ?? 0;
            $data["staff_id"]      = $v['staff_id'];
            $data["year"]          = date("Y", $cronActionBeans->stime);
            $data["month"]         = date("m", $cronActionBeans->stime);
            $data["day"]           = date("d", $cronActionBeans->stime);
            $data["week"]          = date("W", $cronActionBeans->stime);
            $data["value"]         = $v['cnt'];
            $data["action_id"]     = $v['action_id'];
            $data["ctime"]         = time();
            // 写入
            $result = $srvObj->insert($data);
        }

        // 团队跟进统计数据
        $cronActionBeans->type = 2;
        $departmentList = $this->getActionFollowStatisticsForType($cronActionBeans);
        HelperFuns::writeLog("Department YesterdayActionFollow count " . count($staffList), '/yestoday', 'YesterdayActionFollow');

        // 数据处理
        foreach ($departmentList as $v) {
            $data["enterprise_id"] = $v['enterprise_id'];
            $data["department_id"] = $v["department_id"];
            $data["staff_id"]      = 0;
            $data["year"]          = date("Y", $cronActionBeans->stime);
            $data["month"]         = date("m", $cronActionBeans->stime);
            $data["day"]           = date("d", $cronActionBeans->stime);
            $data["week"]          = date("W", $cronActionBeans->stime);
            $data["value"]         = $v['cnt'];
            $data["action_id"]     = $v['action_id'];
            $data["ctime"]         = time();
            // 写入
            $result = $srvObj->insert($data);
        }

        return 1;
    }

    /**
     * 返回跟进的统计
     * @param  \system\beans\cron\CronActionBeans $cronActionBeans
     * @date: 2022-02-15 18:33:20.
     * @author  <mawei.live>
     * @return array
     */
    function getActionFollowStatisticsForType(CronActionBeans $cronActionBeans)
    {
        // 时间过滤
        if (!($cronActionBeans->stime && $cronActionBeans->etime)) {
            return [];
        }

        // 字段
        $field = "enterprise_id,staff_id,department_id,action_id,COUNT(*) cnt";

        // 构建查询
        $query = (new Query())->from(TableMap::ActionFollow)
            ->where([
                "and",
                ["type" => $cronActionBeans->type],
                [">=", "ctime", $cronActionBeans->stime],
                ["<=", "ctime", $cronActionBeans->etime],
            ]);

        // 类型 1.用户 2.部门
        if ($cronActionBeans->type == 1) {
            $query->groupBy("staff_id,action_id");
        } else {
            $query->groupBy("department_id,action_id");
        }

        return $query->select($field)
            ->all();
    }

    //------->>>>>>>------follow_info旧表跟进统计处理------<<<<<<<&&&&>>>>>>---MaWei@2022-02-24 22:41----<<<<<<----//

    /**
     *follow_info 跟进处理
     * date: 2022-02-24 22:39:28
     * @author  <mawei.live>
     * @return void
     */
    function getYesterdayOldFollow($_date = null)
    {
        $cronActionBeans = new CronActionBeans();

        $time = $_date ? strtotime($_date) : date("Y-m-d", strtotime("-1 day"));
        // $time = date("Y-m-d", strtotime("-1 day"));
        $cronActionBeans->stime = strtotime($time);
        $cronActionBeans->etime = strtotime($time . " 23:59:59");

        // 实例化对象
        $srvObj = ServiceFactory::getInstance("BaseDB", TableMap::ActionDayStatisticsLog);

        // 员工跟进统计数据
        $cronActionBeans->type = 1;
        $staffList = $this->getFollowInfoStatistics($cronActionBeans);
        HelperFuns::writeLog("Staff YesterdayOldFollow count " . count($staffList), 'yestoday', 'YesterdayOldFollow');

        // 提取分组
        $uesrIds = array_column($staffList, 'user_id');
        $department = (new Query())->select("id,department ")
            ->from(TableMap::User)
            ->where(["in", "id", $uesrIds])->indexBy("id")->all();

        // 数据处理
        foreach ($staffList as $v) {
            $data["enterprise_id"] = $v['enterprise_id'];
            $data["department_id"] = $department[$v["user_id"]]["department"] ?? 0;
            $data["staff_id"]      = $v['user_id'];
            $data["year"]          = date("Y", $cronActionBeans->stime);
            $data["month"]         = date("m", $cronActionBeans->stime);
            $data["day"]           = date("d", $cronActionBeans->stime);
            $data["week"]          = date("W", $cronActionBeans->stime);
            $data["value"]         = $v['cnt'];
            $data["action_id"]     = $v['action_id'];
            $data["ctime"]         = time();
            // 写入
            $result = $srvObj->insert($data);
        }

        return 1;
    }

    /**
     * 旧跟进统计查询返回 
     * @param  \system\beans\cron\CronActionBeans $cronActionBeans
     * date: 2022-02-24 22:42:18
     * @author  <mawei.live>
     * @return array
     */
    function getFollowInfoStatistics(CronActionBeans $cronActionBeans)
    {
        // 时间过滤
        if (!($cronActionBeans->stime && $cronActionBeans->etime)) {
            return [];
        }

        // 字段
        $field = "enterprise_id,user_id,state AS action_id,COUNT(*) cnt";

        // 构建查询
        $query = (new Query())->from(TableMap::FollowInfo)
            ->where([
                "and",
                [">", "state", 0],
                [">=", "created_at", $cronActionBeans->stime],
                ["<=", "created_at", $cronActionBeans->etime],
            ]);

        return $query->select($field)
            ->groupBy("user_id,action_id")
            ->all();
    }
}
