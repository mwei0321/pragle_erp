<?php

/* 
 * 昨天数据统计定时任务
 * @Author: MaWei 
 * @Date: 2023-04-26 23:00:31 
 * @Last Modified by: MaWei
 */


namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use system\common\{ServiceFactory, TableMap, HelperFuns};

class DevadvController extends Controller
{

    /**
     * 5 2 * * * /domedea/pragle_erp/yii devadv/everyday
     * 每天执行
     * date: 2022-02-27 20:12:58
     * @author  <mawei.live>
     * @return void
     */
    function actionEveryday()
    {

        // 员工每天完成统计
    }
}
