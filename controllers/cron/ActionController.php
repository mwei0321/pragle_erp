<?php

/**
 * 动作定时任务
 * @Author: MaWei
 * @Date:   2021-12-22
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-02-24 23:25:17
 */

namespace app\controllers\cron;

use app\controllers\InitController;
use system\common\{HelperFuns, ServiceFactory, TableMap};
use system\traits\BindBeanParamsTrait;
use system\beans\kpi\ActionBeans;

class ActionController extends InitController
{
    use BindBeanParamsTrait;

    /**
     * 每天执行
     * date: 2022-02-27 20:12:58
     * @author  <mawei.live>
     * @return void
     */
    function actionEveryday()
    {
        $actionBeans = new ActionBeans();
        $actionBeans->cycle = 1;
        // 员工每天完成统计
        ServiceFactory::getInstance("ActionCronSrv")->staffActionFinishCheck($actionBeans);
    }

    /**
     * 每月执行
     * date: 2022-02-27 20:13:13
     * @author  <mawei.live>
     * @return void
     */
    function actionEverymonth()
    {
        $actionBeans = new ActionBeans();
        $actionBeans->cycle = 2;
        // 员工每天完成统计
        ServiceFactory::getInstance("ActionCronSrv")->staffActionFinishCheck($actionBeans);
    }

    /**
     * 每周执行
     * date: 2022-02-27 20:13:13
     * @author  <mawei.live>
     * @return void
     */
    function actionEveryweek()
    {
        $actionBeans = new ActionBeans();
        $actionBeans->cycle = 3;
        // 员工每天完成统计
        ServiceFactory::getInstance("ActionCronSrv")->staffActionFinishCheck($actionBeans);
    }
}
