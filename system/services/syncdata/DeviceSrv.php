<?php

/* 设备同步
 * @Author: MaWei 
 * @Date: 2022-05-22 10:22:20 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-05-22 12:51:09
 */

namespace system\services\syncdata;

use \Yii;
use yii\db\Query;
use system\common\{TableMap, HelperFuns};
use system\beans\sync\SyncBaseBeans;

class DeviceSrv
{

    /**
     * 同步设备信息
     * date: 2022-05-22 10:49:48
     * @author  <mawei.live>
     * @return void
     */
    function syncDeviceByUid(SyncBaseBeans $syncBaseBeans)
    {
        // 提取设备列表
        $list = (new Query())->from(TableMap::TbDevice)
            ->select("Did,uid,CpID,Devno,Company_id")
            ->where([
                "uid" => $syncBaseBeans->from_uid,
                "Delete" => 0,
            ])
            ->all($this->syncFromDB);
        if (count($list) < 1) {
            return 1;
        }
        // 提取
        // $devSn = array_column($list, 'Devno');
        // 过滤设备
        // $existList = $this->checkDevSnIsExsit($devSn);
        // var_dump($existList, $list);
        // exit();
        // 处理设备是否同步
        foreach ($list as $val) {
            // 存在跳过
            // if (!in_array($val['Devno'], $existList)) {
            //     continue;
            // }
            // 提取设备信息
            $devInfo = (new Query())->from(TableMap::TbDevice)->where(['Did' => $val['Did']])->one($this->syncFromDB);
            $oldId = $devInfo['Did'];
            $devInfo['sync_id'] = $oldId;
            $devInfo["Company_id"] = $syncBaseBeans->to_enterprise_id;
            unset($devInfo['Did']);
            $result = $this->syncToDB->createCommand()->insert(TableMap::TbDevice, $devInfo)->execute();
            if ($result === false) {
                HelperFuns::writeLog("error:" . json_encode($devInfo), "syncDevice.log", "update old syncDevice");
                return -1;
            }
            $newId = $this->syncToDB->getLastInsertID();
            // 回写同步id
            $result = $this->syncFromDB->createCommand()->update(TableMap::TbDevice, ["sync_id" => $newId], ['Did' => $oldId])->execute();
            if ($result === false) {
                HelperFuns::writeLog("error:{$oldId}", "syncDevice.log", "update old syncDevice");
                return -3;
            }
            $syncBaseBeans->from_device_no = $devInfo['Devno'];
            $syncBaseBeans->from_device_id = $oldId;
            $syncBaseBeans->to_device_id = $newId;

            // 同步设备统计
            $this->syncDeviceStatisticByDevno($syncBaseBeans);

            // 同步设备状态
            $this->syncDeviceStatusByDevno($syncBaseBeans);

            // 同步设备状态
            $this->syncDevicePushRecByDevno($syncBaseBeans);
        }
        return 1;
    }

    /**
     * 同步设备流量
     * @param  \system\beans\sync\SyncBaseBeans $syncBaseBeans
     * date: 2022-05-29 20:44:37
     * @author  <mawei.live>
     * @return void
     */
    function syncDeviceStatisticByDevno(SyncBaseBeans $syncBaseBeans)
    {
        // 提取设备统计
        $list = (new Query())->from(TableMap::TbStatisticsDevice)
            ->where([
                'devno'   => $syncBaseBeans->from_device_no,
            ])
            ->all($this->syncFromDB);
        if (count($list) < 1) {
            return 1;
        }
        $oldId = 0;
        foreach ($list as $v) {
            $v['CompanyId'] = $syncBaseBeans->to_enterprise_id;
            $v['sync_id']    = $oldId = $v['statistics_id'];
            unset($v['statistics_id']);
            // 插入数据
            $result = $this->syncToDB->createCommand()->insert(TableMap::TbStatisticsDevice, $v)->execute();
            if ($result === false) {
                HelperFuns::writeLog("error:{$oldId}", "device.log", "insert new syncDeviceStatistic");
                return -1;
            }
            $id = $this->syncToDB->getLastInsertID();

            // 更新同步记录
            $result = $this->syncFromDB->createCommand()->update(TableMap::TbStatisticsDevice, ["sync_id" => $id], ['statistics_id' => $oldId])->execute();
            if ($result === false) {
                HelperFuns::writeLog("error:{$id}", "device.log", "update old syncDeviceStatistic");
                return -3;
            }
        }
        return 1;
    }

    /**
     * 同步设备状态
     * @param  \system\beans\sync\SyncBaseBeans $syncBaseBeans
     * date: 2022-06-01 10:51:00
     * @author  <mawei.live>
     * @return void
     */
    function syncDeviceStatusByDevno(SyncBaseBeans $syncBaseBeans)
    {
        // 提取设备统计
        $list = (new Query())->from(TableMap::TbDeviceStatus)
            ->where([
                'devicenum'   => $syncBaseBeans->from_device_no,
            ])
            ->all($this->syncFromDB);
        if (count($list) < 1) {
            return 1;
        }
        $oldId = 0;
        foreach ($list as $v) {
            $v['sync_id'] = $oldId = $v['id'];
            unset($v['id']);
            // 插入数据
            $result = $this->syncToDB->createCommand()->insert(TableMap::TbDeviceStatus, $v)->execute();
            if ($result === false) {
                HelperFuns::writeLog("error:{$oldId}", "device.log", "insert new syncDeviceStatusByDevno");
                return -1;
            }
            $id = $this->syncToDB->getLastInsertID();

            // 更新同步记录
            $result = $this->syncFromDB->createCommand()->update(TableMap::TbDeviceStatus, ["sync_id" => $id], ['id' => $oldId])->execute();
            if ($result === false) {
                HelperFuns::writeLog("error:{$id}", "device.log", "update old syncDeviceStatusByDevno");
                return -3;
            }
        }
        return 1;
    }

    /**
     * 推送记录
     * @param  \system\beans\sync\SyncBaseBeans $syncBaseBeans
     * date: 2022-06-01 11:01:18
     * @author  <mawei.live>
     * @return void
     */
    function syncDevicePushRecByDevno(SyncBaseBeans $syncBaseBeans)
    {
        // 提取设备统计
        $list = (new Query())->from(TableMap::TbPushRec)
            ->where([
                'Devno'   => $syncBaseBeans->from_device_no,
            ])
            ->all($this->syncFromDB);
        if (count($list) < 1) {
            return 1;
        }
        $oldId = 0;
        foreach ($list as $v) {
            $v['sync_id'] = $oldId = $v['Id'];
            unset($v['Id']);
            // 插入数据
            $result = $this->syncToDB->createCommand()->insert(TableMap::TbPushRec, $v)->execute();
            if ($result === false) {
                HelperFuns::writeLog("error:{$oldId}", "device.log", "insert new syncDeviceStatusByDevno");
                return -1;
            }
            $id = $this->syncToDB->getLastInsertID();

            // 更新同步记录
            $result = $this->syncFromDB->createCommand()->update(TableMap::TbPushRec, ["sync_id" => $id], ['Id' => $oldId])->execute();
            if ($result === false) {
                HelperFuns::writeLog("error:{$id}", "device.log", "update old syncDeviceStatusByDevno");
                return -3;
            }
        }
        return 1;
    }

    /**
     * 同步控制
     * @param  \system\beans\sync\SyncBaseBeans $syncBaseBeans
     * date: 2022-06-01 10:54:44
     * @author  <mawei.live>
     * @return void
     */
    function syncDeviceControllerByUid(SyncBaseBeans $syncBaseBeans)
    {
        // 提取设备统计
        $list = (new Query())->from(TableMap::TbControllerList)
            ->where([
                'uid'   => $syncBaseBeans->from_uid,
            ])
            ->all($this->syncFromDB);
        if (count($list) < 1) {
            return 1;
        }
        $oldId = 0;
        foreach ($list as $v) {
            $v['Company_id'] = $syncBaseBeans->to_enterprise_id;
            $v['uid']        = $syncBaseBeans->to_uid;
            $v['sync_id']    = $oldId = $v['id'];
            unset($v['id']);
            // 插入数据
            $result = $this->syncToDB->createCommand()->insert(TableMap::TbControllerList, $v)->execute();
            if ($result === false) {
                HelperFuns::writeLog("error:{$oldId}", "device.log", "insert new syncDeviceControllerByUid");
                return -1;
            }
            $id = $this->syncToDB->getLastInsertID();

            // 更新同步记录
            $result = $this->syncFromDB->createCommand()->update(TableMap::TbControllerList, ["sync_id" => $id], ['id' => $oldId])->execute();
            if ($result === false) {
                HelperFuns::writeLog("error:{$id}", "device.log", "update old syncDeviceControllerByUid");
                return -3;
            }
        }
        return 1;
    }

    /**
     * 同步存取
     * @param  \system\beans\sync\SyncBaseBeans $syncBaseBeans
     * date: 2022-06-01 11:20:30
     * @author  <mawei.live>
     * @return void
     */
    function syncDeviceStockByEnterpriseId(SyncBaseBeans $syncBaseBeans)
    {
        // 提取设备统计
        $list = (new Query())->from(TableMap::TbStock)
            ->where([
                'Company_id'   => $syncBaseBeans->from_enterprise_id,
            ])
            ->all($this->syncFromDB);
        if (count($list) < 1) {
            return 1;
        }

        $oldId = 0;
        foreach ($list as $v) {
            $v['Company_id'] = $syncBaseBeans->to_enterprise_id;
            $v['sync_id']    = $oldId = $v['id'];
            unset($v['id']);
            // 插入数据
            $result = $this->syncToDB->createCommand()->insert(TableMap::TbStock, $v)->execute();
            if ($result === false) {
                HelperFuns::writeLog("error:{$oldId}", "device.log", "insert new syncDeviceStockByEnterpriseId");
                return -1;
            }
            $id = $this->syncToDB->getLastInsertID();

            // 更新同步记录
            $result = $this->syncFromDB->createCommand()->update(TableMap::TbStock, ["sync_id" => $id], ['id' => $oldId])->execute();
            if ($result === false) {
                HelperFuns::writeLog("error:{$id}", "device.log", "update old syncDeviceStockByEnterpriseId");
                return -3;
            }
        }
        return 1;
    }

    /**
     * 同步设备支付记录
     * @param  \system\beans\sync\SyncBaseBeans $syncBaseBeans
     * date: 2022-06-01 10:40:49
     * @author  <mawei.live>
     * @return void
     */
    function syncDevicePayByOrderId(SyncBaseBeans $syncBaseBeans)
    {
        // 提取设备统计
        $list = (new Query())->from(TableMap::TbDevicePay)
            ->where([
                'order_id'   => $syncBaseBeans->from_order_id,
            ])
            ->all($this->syncFromDB);
        if (count($list) < 1) {
            return 1;
        }
        $oldId = 0;
        foreach ($list as $v) {
            $v['company_id'] = $syncBaseBeans->to_enterprise_id;
            $v['uid']        = $syncBaseBeans->to_uid;
            $v['order_id']   = $syncBaseBeans->to_order_id;
            $v['sync_id']    = $oldId = $v['id'];
            unset($v['id']);
            // 插入数据
            $result = $this->syncToDB->createCommand()->insert(TableMap::TbDevicePay, $v)->execute();
            if ($result === false) {
                HelperFuns::writeLog("error:{$oldId}", "device.log", "insert new syncDevicePay");
                return -1;
            }
            $id = $this->syncToDB->getLastInsertID();

            // 更新同步记录
            $result = $this->syncFromDB->createCommand()->update(TableMap::TbDevicePay, ["sync_id" => $id], ['id' => $oldId])->execute();
            if ($result === false) {
                HelperFuns::writeLog("error:{$id}", "device.log", "update old syncDevicePay");
                return -3;
            }
        }
        return 1;
    }


    /**
     * 返回存在的设备的no
     * @param  array $_devSnArr
     * date: 2022-05-22 11:48:32
     * @author  <mawei.live>
     * @return array
     */
    function checkDevSnIsExsit($_devSnArr)
    {
        // 提取存在的devno
        $isExistList = (new Query())->from(TableMap::TbDevice)
            ->where([
                "in", "Devno", $_devSnArr
            ])
            ->select("Devno")
            ->all($this->syncToDB);

        return count($isExistList) > 0 ? array_column($isExistList, "Devno") : [];
    }

    // 构造函数
    function __construct()
    {
        $this->syncFromDB = \Yii::$app->dbcenter_from;
        $this->syncToDB = \Yii::$app->dbcenter_to;
    }

    private $syncToDB;
    private $syncFromDB;
}
