<?php
/*
 * @Author: MaWei 1123265518@qq.com
 * @Date: 2022-03-09 12:32:17
 * @LastEditors: MaWei 1123265518@qq.com
 * @LastEditTime: 2022-05-24 16:46:55
 * @FilePath: \pragle_erp\controllers\erpapi\IndexController.php
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
            "app/Api/V1/Services/OrderService.php",
        ];

        $path = "F:/MaWei/code/ancda_crm/";

        var_dump(HelperFuns::copyFile($path, $a));
    }
}
