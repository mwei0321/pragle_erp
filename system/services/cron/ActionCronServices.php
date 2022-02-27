<?php

/*
 * 动作执行
 * @Author: MaWei 
 * @Date: 2022-02-19 19:45:56 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-02-24 23:30:09
 */

namespace system\services\cron;

use yii\db\Query;
use system\common\{HelperFuns, TableMap, ServiceFactory};
use system\beans\kpi\ActionBeans;
use system\beans\cron\{CronActionBeans, CronActionLogBeans};

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

        // 处理是否完成动作目标
        foreach ($list as $v) {
            // 提取指定员工的动作完成值参数
            $actionBeans->enterprise_id = $v['enterprise_id'];
            $actionBeans->staff_id      = $v['staff_id'];
            $actionBeans->action_id     = $v['action_id'];

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
