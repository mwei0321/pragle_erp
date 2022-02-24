<?php

/* 
 * 昨天数据统计定时任务
 * @Author: MaWei 
 * @Date: 2022-02-24 23:00:31 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-02-24 23:14:02
 */


namespace app\controllers\cron;

use app\controllers\InitController;
use system\common\{ServiceFactory, TableMap};
use system\traits\BindBeanParamsTrait;
use system\beans\kpi\ActionBeans;

class YestodayController extends InitController
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
        // ServiceFactory::getInstance("EmailCronSrv")->getYesterdayEmailStatistics();
        // 昨天新跟进动作统计
        // ServiceFactory::getInstance("FollowCronSrv")->getYesterdayActionFollow();
        // 昨天旧跟进动作统计
        ServiceFactory::getInstance("FollowCronSrv")->getYesterdayOldFollow();
        // 昨天客户统计
        // TD
    }
}
