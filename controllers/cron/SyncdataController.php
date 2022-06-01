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
        set_time_limit(0);

        $syncBaseBeans = new SyncBaseBeans();
        $syncBaseBeans->from_enterprise_id = 264;

        // $connection = \Yii::$app->dbcenter_to->beginTransaction();

        // 同步企业,用户,相关统计
        if (ServiceFactory::getInstance("SyncEnterpriseUserSrv")->syncEnterpriseById($syncBaseBeans) > 0) {
            echo "同步企业成功……";
        }
        if (ServiceFactory::getInstance("SyncEnterpriseUserSrv")->syncUserActiveByEnterpriseId($syncBaseBeans) > 0) {
            echo "同步企业统计成功……";
        }
        if (ServiceFactory::getInstance("SyncDeviceSrv")->syncDeviceStockByEnterpriseId($syncBaseBeans) > 0) {
            echo "同步企业存取成功……";
        }

        // 提取企业下的员工
        $uid = ServiceFactory::getInstance("SyncEnterpriseUserSrv")->getEnterpriseSyncUserId($syncBaseBeans);
        foreach ($uid as $v) {
            $syncBaseBeans->from_uid = $v;
            $this->syncForUid($syncBaseBeans);
        }
    }


    function syncForUid(SyncBaseBeans $syncBaseBeans)
    {
        var_dump($syncBaseBeans);
        // 设备
        if (ServiceFactory::getInstance("SyncDeviceSrv")->syncDeviceByUid($syncBaseBeans) > 0) {
            echo "同步设备成功……";
        }
        if (ServiceFactory::getInstance("SyncDeviceSrv")->syncDeviceControllerByUid($syncBaseBeans) > 0) {
            echo "同步设备控制成功……";
        }

        // 素材
        if (ServiceFactory::getInstance("SyncMaterialSrv")->syncVideoByUid($syncBaseBeans) > 0) {
            echo "同步企业流量成功……";
        }
        if (ServiceFactory::getInstance("SyncMaterialSrv")->syncAnalysisByUid($syncBaseBeans) > 0) {
            echo "同步企业统计分析成功……";
        }
        // 订单支付
        if (ServiceFactory::getInstance("SyncOrderSrv")->syncOrderByUid($syncBaseBeans) > 0) {
            echo "同步订单成功……";
        }
        if (ServiceFactory::getInstance("SyncDeviceSrv")->syncDevicePayByOrderId($syncBaseBeans) > 0) {
            echo "同步设备支付成功……";
        }
        if (ServiceFactory::getInstance("SyncWalletSrv")->syncWallByEnterpriseId($syncBaseBeans) > 0) {
            echo "同步钱包成功……";
        }

        // 流量统计
        if (ServiceFactory::getInstance("SyncFlowSrv")->syncFlowCordByUid($syncBaseBeans) > 0) {
            echo "同步企业流量成功……";
        }
        if (ServiceFactory::getInstance("SyncFlowSrv")->syncFlowRecordByUid($syncBaseBeans) > 0) {
            echo "同步企业流量记录成功……";
        }

        var_dump($syncBaseBeans);
        exit();
    }
}
