<?php

/* 修复数据
 * @Author: MaWei 
 * @Date: 2022-05-15 10:43:23 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-05-15 10:59:32
 */

namespace app\controllers\erpapi;

use yii\base\Controller;
use system\common\{ServiceFactory, TableMap};

class RepairController extends Controller
{

    /**
     * 修复定时任务-每天动作统计
     * date: 2022-05-15 10:54:59
     * @author  <mawei.live>
     * @return void
     */
    function actionCronday()
    {
        for ($i = 12; $i > -1; $i--) {
            $day = date("Y-m-d", strtotime("-{$i} day"));
            var_dump($day);
            exit;

            // 昨天邮件统计 
            ServiceFactory::getInstance("EmailCronSrv")->getYesterdayEmailStatistics($day);
            // // 昨天新跟进动作统计
            ServiceFactory::getInstance("FollowCronSrv")->getYesterdayActionFollow($day);
            // // 昨天旧跟进动作统计
            ServiceFactory::getInstance("FollowCronSrv")->getYesterdayOldFollow($day);
            // 昨天客户统计
            ServiceFactory::getInstance("CustomerCronSrv")->getYesterdayCustomerStatistics($day);
        }
    }

    function actionCronscore()
    {
        $actionBeans = new \system\beans\kpi\ActionBeans();
        $actionBeans->cycle = 1;

        for ($i = 12; $i > 0; $i--) {
            $day = date("Y-m-d", strtotime("-{$i} day"));
            var_dump($day);

            $actionBeans->month = date("m", strtotime($day));
            $actionBeans->day = date("d", strtotime($day));
            $actionBeans->week = date("W", strtotime($day));

            // 员工每天完成统计
            ServiceFactory::getInstance("ActionCronSrv")->staffActionFinishCheck($actionBeans);
        }
    }
}
