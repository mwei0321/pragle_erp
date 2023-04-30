<?php
/*
 * @Author: MaWei 1123265518@qq.com
 * @Date: 2022-03-09 12:32:17
 * @LastEditors: MaWei 1123265518@qq.com
 * @LastEditTime: 2022-07-07
 * @FilePath: \Pragle_erp\controllers\erpapi\IndexController.php
 * @Description: 
 * 
 * Copyright (c) 2022 by MaWei 1123265518@qq.com, All Rights Reserved. 
 */

namespace app\controllers\erpapi;

use yii\web\Controller;
use system\common\{ServiceFactory, HelperFuns, TableMap};
use services\traits\BindBeanParamsTrait;
use yii\db\Query;

class IndexController extends Controller
{

    function actionIndex()
    {
        $a = [
            "app/Api/V1/Controllers/agent/ProfitController.php",
            "app/Api/V1/Controllers/sale/circle/ActivityController.php",
            "app/Console/Commands/VipGiftCommand.php",
            "app/Console/Kernel.php",
            "app/Exports/ProfitExport.php",
            "app/Model/Circle/CircleActivity.php",
            "app/Model/Circle/CircleActivityPrize.php",
            "routes/api.php",
            "app/Api/V1/Controllers/sale/circle/ActivityController.php",
            "app/Api/V1/Controllers/sale/point/PtProductController.php",
            "app/Api/V1/Services/edu/StudentParentService.php",
            "app/Model/Circle/CircleActivityAward.php",
            "app/Model/Circle/CircleActivityCheckIn.php",
            "routes/api.php",
            "app/Api/V1/Controllers/sale/circle/ActivityController.php",
            "app/Console/Commands/VipGiftCommand.php",
            "app/Exports/CircleActivityAwardExport.php",
            "routes/api.php",
            "app/Api/V1/Controllers/sale/circle/ActivityController.php",
            "app/Exports/ProfitExport.php",
            "app/Jobs/PushTaskQueue.php",
        ];

        $path = "E:/GoCode/ancda/ancda_crm/";

        $list = HelperFuns::copyFile($path, $a);
        var_dump($list['count'], sort($list['list']));
        exit();
    }

    function actionSt()
    {
        $day = date("Y-m-d");
        $table = "tbPlayLog" . date("Ymd");
        $list = (new Query())->from($table)
            ->select("`devno`,count(`tplid`) adv_num,SUM(`end`-`start`) play_time")
            ->groupBy("devno")
            ->all(\Yii::$app->dbdata);

        var_dump($list);
        exit();

        foreach ($list as $v) {
            $v['date'] = $day;
            $result = \Yii::$app->db->createCommand()->insert(TableMap::DeviceAdvStatistic, $v)->execute();
            if ($result === false) {
                HelperFuns::writeLog("error:" . json_encode($v), "advdevstatistic.log", "insert new cornDevAdvStatistic");
            }
        }

        $list = (new Query())->from($table)
            ->select("`tplid` adv_id,count(`devno`) device_num,SUM(`end`-`start`) play_time")
            ->groupBy("tplid")
            ->all(\Yii::$app->dbdata);

        foreach ($list as $v) {
            $v['date'] = $day;
            $result = \Yii::$app->db->createCommand()->insert(TableMap::AdvDeviceStatistic, $v)->execute();
            if ($result === false) {
                HelperFuns::writeLog("error:" . json_encode($v), "advdevstatistic.log", "insert new cornAdvDevStatistic");
            }
        }
    }
}
