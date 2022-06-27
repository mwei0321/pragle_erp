<?php

/* 数据同步
 * @Author: MaWei 
 * @Date: 2022-05-17 19:36:22 
 * @Last Modified by:   admin
 * @Last Modified time: 2022-06-27 13:40:39
 */

namespace app\controllers\erpapi;

use app\controllers\InitController;
use GuzzleHttp\Psr7\Query;
use system\common\ServiceFactory;
use system\beans\sync\SyncBaseBeans;
use system\common\HelperFuns;

class SyncdataController extends InitController
{

    function actionSync()
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        $syncBaseBeans = new SyncBaseBeans();
        // 实例化对象并调用
        ServiceFactory::getInstance("SyncEnterpriseUserSrv")->getSyncEnterpriseId($syncBaseBeans);
        if ($syncBaseBeans->to_enterprise_id > 1) {
            HelperFuns::writeLog("no need sync", "syncexist.txt", "enterprise exist");
            echo "不需要同步:" . json_encode($syncBaseBeans->toArray());
            return 1;
        }

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
            HelperFuns::writeLog(json_encode($syncBaseBeans->toArray()), "sync.txt", "sync success");
        }
    }

    function actionPlay()
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');
        $syncToDB = \Yii::$app->dbcenter_to;

        $info = (new \yii\db\Query())->from("sync_play")->where(["utime" => 0])->one($syncToDB);
        // var_dump($info);
        $syncBaseBeans = new SyncBaseBeans();
        $syncBaseBeans->from_uid = $info['from_uid'];
        $syncBaseBeans->to_uid = $info['to_uid'];
        $syncBaseBeans->to_enterprise_id = $info['to_enterprise_id'];
        $syncBaseBeans->to_parent_enterpirse = $info["to_parent_enterprise"];
        $syncToDB->createCommand()->update("sync_play", ["utime" => time()], ['id' => $info['id']])->execute();
        // 节目
        $result = ServiceFactory::getInstance("SyncPlaySrv")->syncAddverByUId($syncBaseBeans);
        // var_dump($result);
        if ($result > 0) {
            echo "同步用户素材成功……";
        }
        if (ServiceFactory::getInstance("SyncPlaySrv")->syncPlayListByAdId($syncBaseBeans) > 0) {
            echo "同步用户节目成功……";
        }
    }

    function actionTest()
    {
        set_time_limit(0);
        ini_set('memory_limit', '256M');

        $syncBaseBeans = new SyncBaseBeans();
        // $syncBaseBeans->from_enterprise_id = 1;
        $syncBaseBeans->from_enterprise_id = 145080;
        // $syncBaseBeans->from_enterprise_id = 1458;
        // $syncBaseBeans->from_parent_enterprise = 338;
        // $syncBaseBeans->from_uid = 141745;
        // $syncBaseBeans->to_enterprise_id = 123;
        // $syncBaseBeans->to_parent_enterprise = 123;
        // $syncBaseBeans->to_uid = 123;


        // ServiceFactory::getInstance("SyncPlaySrv")->syncAddverByUId($syncBaseBeans);
        // ServiceFactory::getInstance("SyncPlaySrv")->syncPlayListByAdId($syncBaseBeans);

        // var_dump($syncBaseBeans);
        // exit();


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
            echo "同步成功:" . json_encode($syncBaseBeans->toArray());
        }
    }


    function syncForUid(SyncBaseBeans $syncBaseBeans)
    {
        // 同步企业员工
        if (ServiceFactory::getInstance("SyncEnterpriseUserSrv")->syncUserInfoById($syncBaseBeans) > 0) {
            echo "同步企业员工成功……";
        }
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
        // 节目
        // if (ServiceFactory::getInstance("SyncPlaySrv")->syncAddverByUId($syncBaseBeans) > 0) {
        //     echo "同步企业流量成功……";
        // }
        // ServiceFactory::getInstance("SyncPlaySrv")->syncPlayListByAdId($syncBaseBeans);

        // if (ServiceFactory::getInstance("SyncWalletSrv")->syncWallByEnterpriseId($syncBaseBeans) > 0) {
        //     echo "同步钱包成功……";
        // }

        // if (ServiceFactory::getInstance("SyncDeviceSrv")->syncDevicePayByOrderId($syncBaseBeans) > 0) {
        //     echo "同步设备支付成功……";
        // }

        // 订单支付
        // if (ServiceFactory::getInstance("SyncOrderSrv")->syncOrderByUid($syncBaseBeans) > 0) {
        //     echo "同步订单成功……";
        // }
        // 流量统计
        if (ServiceFactory::getInstance("SyncFlowSrv")->syncFlowCordByUid($syncBaseBeans) > 0) {
            echo "同步企业流量成功……";
        }
        if (ServiceFactory::getInstance("SyncFlowSrv")->syncFlowRecordByUid($syncBaseBeans) > 0) {
            echo "同步企业流量记录成功……";
        }

        // if (ServiceFactory::getInstance("SyncMaterialSrv")->syncAnalysisByUid($syncBaseBeans) > 0) {
        //     echo "同步企业统计分析成功……";
        // }
    }
}
