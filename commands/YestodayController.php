<?php

/* 
 * 昨天数据统计定时任务
 * @Author: MaWei 
 * @Date: 2022-02-24 23:00:31 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-02-24 23:21:57
 */


namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use system\common\{ServiceFactory, TableMap, HelperFuns};

class YestodayController extends Controller
{


    /**
     * 执行昨天数据统计
     * date: 2022-02-24 22:55:00
     * @author  <mawei.live>
     * @return void
     */
    function actionIndex()
    {
        // 昨天邮件统计 
        ServiceFactory::getInstance("EmailCronSrv")->getYesterdayEmailStatistics();
        // 昨天新跟进动作统计
        ServiceFactory::getInstance("FollowCronSrv")->getYesterdayActionFollow();
        // 昨天旧跟进动作统计
        ServiceFactory::getInstance("FollowCronSrv")->getYesterdayOldFollow();
        // 昨天客户统计
        ServiceFactory::getInstance("CustomerCronSrv")->getYesterdayCustomerStatistics();

        return ExitCode::OK;
    }

    function actionModifyday() {
        for ($i = 60;$i > 0;$i--) {
            $day = date("Y-m-d", strtotime("-{$i} day"));
            var_dump($day);

            // 昨天邮件统计 
            ServiceFactory::getInstance("EmailCronSrv")->getYesterdayEmailStatistics($day);
            // 昨天新跟进动作统计
            ServiceFactory::getInstance("FollowCronSrv")->getYesterdayActionFollow($day);
            // 昨天旧跟进动作统计
            ServiceFactory::getInstance("FollowCronSrv")->getYesterdayOldFollow($day);
            // 昨天客户统计
            ServiceFactory::getInstance("CustomerCronSrv")->getYesterdayCustomerStatistics($day);
        }
    }

    function actionModifysocre() {
        $actionBeans = new \system\beans\kpi\ActionBeans();
        $actionBeans->cycle = 1;

        for ($i = 60;$i > 0;$i--) {
            $day = strtotime("Y-m-d", strtotime("-{$i} day"));
            var_dump($day);
            
            $actionBeans->month = date("m",strtotime($day));
            $actionBeans->day = date("d",strtotime($day));
            $actionBeans->week = date("W",strtotime($day));

            // 员工每天完成统计
            ServiceFactory::getInstance("ActionCronSrv")->staffActionFinishCheck($actionBeans);
        }
    }
}
