<?php

/* 数据同步
 * @Author: MaWei 
 * @Date: 2022-05-17 19:36:22 
 * @Last Modified by:   admin
 * @Last Modified time: 2022-06-27 13:40:39
 */

namespace app\controllers\erpapi;

use app\controllers\InitController;
use system\common\ServiceFactory;
use system\beans\sync\SyncBaseBeans;
use system\common\HelperFuns;

class SyncdataController extends InitController
{

    function actionModify()
    {
        // TbEnterprise
        // TbUser
        // TbUserInfo
        // TbDevice
        // TbVedio
        // TbMakeAddver
        // TbPlayList
        // TbPlayListPlan
        // TbFlowCord
        // TbFlowRecord

        $sql = "SELECT GROUP_CONCAT(Company_id) cid,GROUP_CONCAT(u.`uid`) uids,GROUP_CONCAT(ui.`id`) ufids,Email,COUNT(*) cnt,GROUP_CONCAT(u.sync_id) ouid FROM tbUserinfo ui LEFT JOIN tbUser u ON u.uid=ui.uid WHERE ui.sync_id > 0 GROUP BY Email HAVING cnt > 1 order by cnt desc";
        $list = \Yii::$app->dbcenter_to->createCommand($sql)->queryAll();

        foreach ($list as $k => $v) {
            $cid = explode(",",$v["cid"]);
            $uids = explode(",",$v["uids"]);
            $ufids = explode(",",$v["ufids"]);
            $ouid = explode(",",$v["ouid"]);
            $data = [];
            $data['email'] = $v['Email'];
            $data['group_id'] = $k;
            
            foreach ($cid as $k => $v) {
                $tmp = \Yii::$app->dbcenter_to->createCommand("select parentID from tbenterprise where id = {$v}")->queryOne();
                $data["pcid"] = $tmp['parentID'];
                $data['userinfo_id'] = $ufids[$k];
                $data['new_uid'] = $uids[$k];
                $data['new_enterprise_id'] = $v;
                $data['old_uid'] = $ouid[$k];
                $result = \Yii::$app->dbcenter_to->createCommand()->insert('sync_modify',$data)->execute();
                var_dump($result);
            }
        }
    }

    function actionModify2() {
        $file = ROOT_PATH."/sync_modifiy.txt";
        $file2 = ROOT_PATH."/sync_modifiy1.txt";
        $file3 = ROOT_PATH."/sync_modifiy_log.txt";
        $groupid = HelperFuns::rFile($file,0);
        $isRun = HelperFuns::rFile($file2,0);
        $dbObj = \Yii::$app->dbcenter_to;
        if($isRun == 0) {
            $result = HelperFuns::writeFile(1, $file2,0);
            if (!$result) {
                echo "写文件失败";
                return false;
            }
            $sql = "select * from sync_modify where group_id = {$groupid}";
            $list = $dbObj->createCommand($sql)->queryAll();
            $connection = $dbObj->beginTransaction();
            $uid = 0;
            $cid = 0;
            $pid = 0;
            foreach ($list as $k => $v) {
                if ($k == 0) {
                    $uid = $v["new_uid"];
                    $cid = $v["new_enterprise_id"];
                    $pid = $v["pcid"];
                    $result = HelperFuns::writeFile("<<<<<----<<<<<----<<<<<----<start>------<group:{$groupid};email:".$v["email"].">-----<start>------>>>>>" . "\r\n", $file3, 1);
                    // dev
                    $sql = "UPDATE `tbDevice` SET `uid` = {$uid},`CpID` = {$pid} WHERE `Company_id` = " . $v["new_enterprise_id"];
                    $result = HelperFuns::writeFile($sql . "\r\n", $file3, 1);
                    echo $sql . "<br>";
                    $result = $dbObj->createCommand($sql)->execute();
                    // 判断是否成功
                    if ($result === false) {
                        //失败回滚
                        $connection->rollback();
                        echo -7;
                        return false;
                    }
                    continue;
                }
                echo "开始执行";
                $result = HelperFuns::writeFile(">>----<start>------<uid:{$uid}>-----<start>------<<<<" . "\r\n", $file3, 1);
                // 删除用户
                $sql = "DELETE FROM `tbflowcord` WHERE `uid` = ".$v["new_uid"];
                $result = $dbObj->createCommand($sql)->execute();
                // 判断是否成功
                if ($result === false) {
                    //失败回滚
                    $connection->rollback();
                    echo -1;
                    return false;
                }
                $sql = "DELETE FROM `tbflowrecord` WHERE `uid` = ".$v["new_uid"];
                $result = $dbObj->createCommand($sql)->execute();
                // 判断是否成功
                if ($result === false) {
                    //失败回滚
                    $connection->rollback();
                    echo -2;
                    return false;
                }
                $sql = "DELETE FROM `tbmakeaddver` WHERE `uid` = ".$v["new_uid"];
                $result = HelperFuns::writeFile($sql."\r\n", $file3, 1);
                $result = $dbObj->createCommand($sql)->execute();
                // 判断是否成功
                if ($result === false) {
                    //失败回滚
                    $connection->rollback();
                    echo -3;
                    return false;
                }

                $sql = "DELETE FROM `tbUser` WHERE `uid` = ".$v["new_uid"];
                $result = HelperFuns::writeFile($sql . "\r\n", $file3, 1);
                $result = $dbObj->createCommand($sql)->execute();
                // 判断是否成功
                if ($result === false) {
                    //失败回滚
                    $connection->rollback();
                    echo -4;
                    return false;
                }

                $sql = "DELETE FROM `tbUserinfo` WHERE `uid` = ".$v["new_uid"];
                $result = HelperFuns::writeFile($sql . "\r\n", $file3, 1);
                $result = $dbObj->createCommand($sql)->execute();
                // 判断是否成功
                if ($result === false) {
                    //失败回滚
                    $connection->rollback();
                    echo -5;
                    return false;
                }

                $sql = "DELETE FROM `tbenterprise` WHERE `id` = ".$v["new_enterprise_id"];
                $result = HelperFuns::writeFile($sql . "\r\n", $file3, 1);
                $result = $dbObj->createCommand($sql)->execute();
                // 判断是否成功
                if ($result === false) {
                    //失败回滚
                    $connection->rollback();
                    echo -6;
                    return false;
                }
                // dev
                $sql = "UPDATE `tbDevice` SET `uid` = {$uid},`CpID` = {$pid},`Company_id`={$cid} WHERE `Company_id` = ".$v["new_enterprise_id"];
                $result = HelperFuns::writeFile($sql . "\r\n", $file3, 1);
                echo $sql."<br>";
                $result = $dbObj->createCommand($sql)->execute();
                // 判断是否成功
                if ($result === false) {
                    //失败回滚
                    $connection->rollback();
                    echo -7;
                    return false;
                }

                $sql = "UPDATE `sync_modify` SET `utime` = ".time().",`status` = 1 WHERE `id` = ".$v["id"];
                $result = HelperFuns::writeFile($sql . "\r\n", $file3, 1);
                $result = $dbObj->createCommand($sql)->execute();
                // 判断是否成功
                if ($result === false) {
                    //失败回滚
                    echo -8;
                    $connection->rollback();
                    return false;
                }

                $result = HelperFuns::writeFile(">>>>>----<end>------<uid:{$uid}>-----<end>-----<<<<" . "\r\n\r\n", $file3, 1);
            }
            
            // 提交事务
            $connection->commit();
            $result = HelperFuns::writeFile($groupid + 1, $file, 0);
            $result = HelperFuns::writeFile(0, $file2, 0);
            echo "执行成功!";
            // 
            $result = HelperFuns::writeFile("<<<<<----<<<<<----<<<<<----<end>------<group:{$groupid};uid:{$uid}>------<end>----->>>>>" . "\r\n", $file3, 1);
            $result = HelperFuns::writeFile("\r\n", $file3, 1);
        } else {
            echo "上次没有执行完";
        }
    }

    function actionSync()
    {
        var_dump(11111);
        exit();
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


    function actionSyncplay () {
        $syncBaseBeans = new SyncBaseBeans();
        $syncBaseBeans->from_uid = 2221;
        $syncBaseBeans->to_uid = 145913;
        $syncBaseBeans->to_enterprise_id = 148530;
        $syncBaseBeans->to_parent_enterpirse = 1;


        // 素材
        if (ServiceFactory::getInstance("SyncMaterialSrv")->syncVideoByUid($syncBaseBeans) > 0) {
            echo "同步企业素材成功……";
        }

        // 节目
        $result = ServiceFactory::getInstance("SyncPlaySrv")->syncAddverByUId($syncBaseBeans);
        if ($result > 0) {
            echo "同步用户节目成功……";
        }
        if (ServiceFactory::getInstance("SyncPlaySrv")->syncPlayListByAdId($syncBaseBeans) > 0) {
            echo "同步用户播放计划成功……";
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
