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
use services\common\{ServiceFactory, TableMap};
use services\traits\BindBeanParamsTrait;
use system\common\HelperFuns;

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

        $path = "D:/Code/Ancda/PHP/ancda_crm/";

        var_dump(HelperFuns::copyFile($path, $a));
    }
}
