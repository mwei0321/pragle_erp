<?php

/*
 * 动作执行
 * @Author: MaWei 
 * @Date: 2022-02-19 19:45:56 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-04-06 14:37:24
 */

namespace system\services\cron;

use yii\db\Query;
use system\common\{TableMap, ServiceFactory};
use system\beans\kpi\ActionBeans;
use system\beans\cron\{CronActionLogBeans};

class ActionCronServices
{

    /**
     * 员工每天动作完成统计
     * @date: 2022-02-19 11:53:39
     * @author  <mawei.live>
     * @return void
     */
    function staffActionFinishCheck(ActionBeans $actionBeans)
    {
        // 提取每天员工动作KPI
        $actionBeans->year  = $actionBeans->year ?: date('Y');

        // 按周期处理
        switch ($actionBeans->cycle) {
            case 1: // 每天
                $actionBeans->month = $actionBeans->month ?: date('m');
                $actionBeans->day   = $actionBeans->day ?: date('d');
                break;
            case 2: // 每月
                $actionBeans->month = $actionBeans->month ?: date('m');
                break;
            case 3: // 每周
                $actionBeans->week  = $actionBeans->week ?: date('W');
                break;
            default:
                break;
        }

        // 提取员工动作KPI目标
        $list = ServiceFactory::getInstance("BaseDB", TableMap::StaffActionKpi)
            ->getListByCondition([
                "and",
                ["cycle"    => $actionBeans->cycle],
                ["del_time" => 0],
                ["year"     => $actionBeans->year],
                [">", "action_value", 0],
            ], "id,action_id,staff_id,enterprise_id,action_value target");
        if (!$list) {
            return -1;
        }

        // 提取动作积分
        $actionScore = ServiceFactory::getInstance("BaseDB", TableMap::Config)
            ->getListByCondition(["in", "id", array_column($list, "action_id")], "id,interior_rank");
        $actionScore = array_combine(array_column($actionScore, 'id'), array_column($actionScore, "interior_rank"));

        // 提取员工动作完成对象
        $actionLogSrv = ServiceFactory::getInstance("ActionLogSrv");
        $finishActionObj = ServiceFactory::getInstance("ActionScoreSrv");

        // 提取员工部门
        $uesrIds = array_column($list, 'staff_id');
        $department = (new Query())->select("id,department")
            ->from(TableMap::User)
            ->where(["in", "id", $uesrIds])->indexBy("id")->all();

        // 处理是否完成动作目标
        foreach ($list as $v) {
            // 提取指定员工的动作完成值参数
            $actionBeans->enterprise_id = $v['enterprise_id'];
            $actionBeans->department_id = $department[$v["staff_id"]]["department"] ?? 0;
            $actionBeans->staff_id      = $v['staff_id'];
            $actionBeans->action_id     = $v['action_id'];

            // 写入执行记录
            $CronActionLogBeans               = new CronActionLogBeans();
            $CronActionLogBeans->setVals($actionBeans->toArray());
            $CronActionLogBeans->target       = $v['target'];
            $CronActionLogBeans->finish       = $actionLogSrv->getAssginActionFinishNum($actionBeans);
            $CronActionLogBeans->status       = ($CronActionLogBeans->finish >= $CronActionLogBeans->target) ? 1 : 0;
            $CronActionLogBeans->obj_id       = $v['id'];
            $CronActionLogBeans->action_score = $actionScore[$v["action_id"]] ?? 0;
            $CronActionLogBeans->ctime        = strtotime("{$actionBeans->year}-{$actionBeans->month}-{$actionBeans->day} 02:00:00");

            // 处理异形处理,比如完成多少得多少分
            if (in_array($v["action_id"], ['228'])) {
                $CronActionLogBeans->status = 1;
                $CronActionLogBeans->finish_score = round($CronActionLogBeans->finish  * $CronActionLogBeans->action_score);
            } else {
                $CronActionLogBeans->finish_score = ($CronActionLogBeans->status > 0 ? (($CronActionLogBeans->target + 1) * $CronActionLogBeans->action_score) : $CronActionLogBeans->finish  * $CronActionLogBeans->action_score);
            }
            $actionLogId = $this->addActionCronLog($CronActionLogBeans);
            if ($actionLogId < 1) {
                return -1;
            }

            // 判断有分完成,完成加积分
            if ($CronActionLogBeans->finish_score > 0) {
                // 添加积分
                $actionScoreBeans         = new \system\beans\score\ActionScoreBeans();
                $actionScoreBeans->setVals($CronActionLogBeans->toArray());
                $actionScoreBeans->score  = $CronActionLogBeans->finish_score;
                $actionScoreBeans->year   = $actionBeans->year;
                $actionScoreBeans->month  = $actionBeans->month ?: date("m");
                $actionScoreBeans->day    = $actionBeans->day ?: date("d");
                $actionScoreBeans->obj_id = $actionLogId;
                $actionScoreBeans->type   = 1;
                // 添加积分入库
                $result = $finishActionObj->addActionFinishScore($actionScoreBeans);
                if ($result < 1) {
                    return -2;
                }
            }

            // 给员工加积分
            // ServiceFactory::getInstance("BaseDB", TableMap::User)->increment("score", "`id` = " . $actionBeans->staff_id, TableMap::User, $CronActionLogBeans->score);
            // }
        }

        return 1;
    }

    /**
     * 团队动作完成统计
     * @date: 2022-02-19 11:53:39
     * @author  <mawei.live>
     * @return void
     */
    function departmentActionFinishCheck(ActionBeans $actionBeans)
    {
        // 提取每天员工动作KPI
        $actionBeans->year  = date('Y');

        // 按周期处理
        switch ($actionBeans->cycle) {
            case 1: // 每天
                $actionBeans->month = date('m');
                $actionBeans->day   = date('d');
                break;
            case 2: // 每月
                $actionBeans->month = date('m');
                break;
            case 3: // 每周
                $actionBeans->week  = date('W');
                break;
            default:
                break;
        }

        // 提取团队动作KPI目标
        $list = ServiceFactory::getInstance("BaseDB", TableMap::DepartmentActionKpi)
            ->getListByCondition([
                "and",
                ["cycle"    => $actionBeans->cycle],
                ["del_time" => 0],
                ["year"     => $actionBeans->year],
                [">", "action_value", 0],
            ], "id,action_id,department_id,enterprise_id,action_value target");
        if (!$list) {
            return -1;
        }

        // 提取动作积分
        $actionScore = ServiceFactory::getInstance("BaseDB", TableMap::Config)
            ->getListByCondition(["in", "id", array_column($list, "action_id")], "id,interior_rank");
        $actionScore = array_combine(array_column($actionScore, 'id'), array_column($actionScore, "interior_rank"));

        // 提取部门动作完成对象
        $actionLogSrv = ServiceFactory::getInstance("ActionLogSrv");
        $finishActionObj = ServiceFactory::getInstance("ActionScoreSrv");

        // // 提取部门部门
        // $uesrIds = array_column($list, 'department_id');
        // $department = (new Query())->select("id,group AS department")
        //     ->from(TableMap::User)
        //     ->where(["in", "id", $uesrIds])->indexBy("id")->all();

        // 日期
        $timem = strtotime("-1 day");
        $year = date('Y', $timem);
        $month = date('m', $timem);
        $day = date('d', $timem);

        // 处理是否完成动作目标
        foreach ($list as $v) {
            // 提取指定部门的动作完成值参数
            $actionBeans->enterprise_id = $v['enterprise_id'];
            $actionBeans->department_id = $department[$v["staff_id"]]["id"] ?? 0;
            $actionBeans->staff_id      = $v['staff_id'];
            $actionBeans->action_id     = $v['action_id'];
            $actionBeans->year = $year;
            $actionBeans->month = $month;
            $actionBeans->day = $day;

            // 写入执行记录
            $CronActionLogBeans         = new CronActionLogBeans();
            $CronActionLogBeans->setVals($actionBeans->toArray());
            $CronActionLogBeans->target = $v['target'];
            $CronActionLogBeans->finish = $actionLogSrv->getAssginActionFinishNum($actionBeans);
            $CronActionLogBeans->status = ($CronActionLogBeans->finish >= $CronActionLogBeans->target) ? 1 : 0;
            $CronActionLogBeans->obj_id = $v['id'];
            $CronActionLogBeans->score  = $actionScore[$v["action_id"]] ?? 0;
            $actionLogId = $this->addActionCronLog($CronActionLogBeans);
            if ($actionLogId < 1) {
                return -1;
            }

            // 判断是否完成,完成加积分
            if ($CronActionLogBeans->status == 1) {
                // 添加积分
                $actionScoreBeans         = new \system\beans\score\ActionScoreBeans();
                $actionScoreBeans->setVals($CronActionLogBeans->toArray());
                $actionScoreBeans->year   = $actionBeans->year;
                $actionScoreBeans->month  = $actionBeans->month ?: date("m");
                $actionScoreBeans->day    = $actionBeans->day ?: date("d");
                $actionScoreBeans->obj_id = $actionLogId;
                $actionScoreBeans->type   = 1;
                // 添加积分入库
                $result = $finishActionObj->addActionFinishScore($actionScoreBeans);
                if ($result < 1) {
                    return -2;
                }

                // 给部门加积分
                // ServiceFactory::getInstance("BaseDB", TableMap::User)->increment("score", "`id` = " . $actionBeans->staff_id, TableMap::User, $CronActionLogBeans->score);
            }
        }

        return 1;
    }

    /**
     * 动作日志入库
     * @param  \system\beans\cron\CronActionLogBeans $CronActionLogBeans
     * date: 2022-02-19 21:05:26
     * @author  <mawei.live>
     * @return int
     */
    function addActionCronLog(CronActionLogBeans $CronActionLogBeans)
    {
        // 入库数据
        $data = $CronActionLogBeans->toArray();
        $data['ctime'] = time();

        // 实例化对象并调用
        return ServiceFactory::getInstance("BaseDB", TableMap::ActionCronLog)->insert($data);
    }
}
