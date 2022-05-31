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
use system\beans\sync\SyncBaseBeans;

class SyncdataController extends InitController
{

    function actionUser()
    {

        ServiceFactory::getInstance("SyncEnterpriseUserSrv")->syncEnterpriseUser();
    }

    function actionTest()
    {
        $syncData = new SyncBaseBeans();
        $syncData->from_uid           = 195;
        $syncData->from_enterprise_id = 185;
        $syncData->to_uid             = 196;
        $syncData->to_enterprise_id   = 195;
        // ServiceFactory::getInstance("SyncOrderSrv")->syncOrderByUid($syncData);

        // 同步企业,用户
        // $srvObj = ServiceFactory::getInstance("SyncEnterpriseUserSrv")->syncEnterprise($syncData);
        $srvObj = ServiceFactory::getInstance("SyncEnterpriseUserSrv")->syncUserInfo($syncData);

        // 流量统计
        // $srvObj = ServiceFactory::getInstance("SyncFlowSrv")->syncFlowCordByUid($syncData);
        // $srvObj = ServiceFactory::getInstance("SyncFlowSrv")->syncFlowRecordByUid($syncData);
        // $srvObj = ServiceFactory::getInstance("SyncDeviceSrv")->syncDeviceStatistic($syncData);


        var_dump($srvObj);
        exit();
    }
}
