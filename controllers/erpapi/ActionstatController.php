<?php

/**
 * 动作统计 
 * @Author: MaWei 
 * @Date: 2022-04-09 20:03:16 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-04-09 21:12:56
 */

namespace app\controllers\erpapi;

use app\controllers\InitController;
use system\common\{ServiceFactory, TableMap};
use system\traits\BindBeanParamsTrait;
use system\beans\statistic\ActionStatBeans;



class ActionstatController extends InitController
{
    use BindBeanParamsTrait;

    /**
     * 返回动作某个时间段统计
     * @param  ActionStatBeans $statBeans
     * date: 2022-04-09 20:09:45
     * @author  <mawei.live>
     * @return void
     */
    function actionTimebucket(ActionStatBeans $statBeans)
    {
        // 参数过滤
        if ($statBeans->enterprise_id < 1 || !$statBeans->stime || !$statBeans->etime) {
            return $this->reJson([$statBeans], 'param error', 400);
        }

        // 提取结果
        $list = ServiceFactory::getInstance("ActionStatSrv")->getActionStatisticListForDate($statBeans);

        // 返回
        return $this->reJson([
            'items' => $list,
            'count' => $statBeans->count,
        ]);
    }
}
