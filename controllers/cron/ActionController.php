<?php

/**
 * 动作定时任务
 * @Author: MaWei
 * @Date:   2021-12-22
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-01-25 22:58:42
 */

namespace app\controllers\cron;

use app\controllers\InitController;
use system\common\{ServiceFactory, TableMap};
use system\traits\BindBeanParamsTrait;
use system\beans\kpi\ActionBeans;

class ActionController extends InitController
{
    use BindBeanParamsTrait;

    function actionStaff(ActionBeans $actionBeans)
    {

        // 员工每天完成统计
        $result = ServiceFactory::getInstance("ActionCronSrv")->staffActionFinishCheck($actionBeans);

        return $this->reJson($result);
    }
}
