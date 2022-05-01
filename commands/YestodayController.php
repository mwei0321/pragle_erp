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
}
