<?php

/* 数据同步
 * @Author: MaWei 
 * @Date: 2022-05-17 19:36:22 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-05-17 19:37:28
 */

namespace app\controllers\cron;

use app\controllers\InitController;
use system\common\ServiceFactory;

class SyncdataController extends InitController
{

    function actionUser()
    {

        ServiceFactory::getInstance("SyncEnterpriseUserSrv")->syncEnterpriseUser();
    }
}
