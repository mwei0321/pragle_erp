<?php

/* 钱包同步
 * @Author: MaWei 
 * @Date: 2022-05-29 15:59:28 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-05-29 16:57:01
 */

namespace system\services\syncdata;

use yii\db\Query;
use system\common\{TableMap, HelperFuns};
use system\beans\sync\SyncBaseBeans;

class FlowSrv
{
    /**
     * 根据uid同步流量
     * @param \system\beans\sync\SyncBaseBeans $syncBaseBeans
     * date: 2022-05-28 16:51:24
     * @author <mawei.live>
     * @return void
     */
    function syncFlowCordByUid(SyncBaseBeans $syncBaseBeans)
    {
        // 提取用户钱包
        $list = (new Query())->from(TableMap::TbFlowCord)
            ->where([
                'uid'     => $syncBaseBeans->from_uid,
                "sync_id" => 0,
            ])
            ->all($this->syncFromDB);
        if (count($list) < 1) {
            return 1;
        }
        $oldId = 0;
        foreach ($list as $v) {
            $v['uid']        = $syncBaseBeans->to_uid;
            $v['Company_id'] = $syncBaseBeans->to_enterprise_id;
            $v['sync_id']    = $oldId = $v['id'];
            unset($v['id']);
            // 插入数据
            $result = $this->syncToDB->createCommand()->insert(TableMap::TbFlowCord, $v)->execute();
            if ($result === false) {
                HelperFuns::writeLog("error:{$oldId}", "flow.log", "insert new flowcord");
            }
            $id = $this->syncToDB->getLastInsertID();

            // 更新同步记录
            $result = $this->syncFromDB->createCommand()->update(TableMap::TbFlowCord, ["sync_id" => $id], ['id' => $oldId])->execute();
            if ($result === false) {
                HelperFuns::writeLog("error:{$oldId}", "flow.log", "update old flowcord");
            }
        }
    }
    /**
     * 根据uid同步钱包
     * @param \system\beans\sync\SyncBaseBeans $syncBaseBeans
     * date: 2022-05-28 16:51:24
     * @author <mawei.live>
     * @return void
     */
    function syncFlowRecordByUid(SyncBaseBeans $syncBaseBeans)
    {
        // 提取用户钱包
        $list = (new Query())->from(TableMap::TbFlowRecord)
            ->where([
                'uid'     => $syncBaseBeans->from_uid,
                "sync_id" => 0,
            ])
            ->all($this->syncFromDB);
        if (count($list) < 1) {
            return 1;
        }
        $oldId = 0;
        foreach ($list as $v) {
            $v['uid']        = $syncBaseBeans->to_uid;
            $v['Company_id'] = $syncBaseBeans->to_enterprise_id;
            $v['sync_id']    = $oldId = $v['id'];
            unset($v['id']);
            // 插入数据
            $result = $this->syncToDB->createCommand()->insert(TableMap::TbFlowRecord, $v)->execute();
            if ($result === false) {
                HelperFuns::writeLog("error:{$oldId}", "flowrecord.log", "insert new flowrecord");
            }
            $id = $this->syncToDB->getLastInsertID();

            // 更新同步记录
            $result = $this->syncFromDB->createCommand()->update(TableMap::TbFlowRecord, ["sync_id" => $id], ['id' => $oldId])->execute();
            if ($result === false) {
                HelperFuns::writeLog("error:{$oldId}", "flowrecord.log", "update old flowrecord");
            }
        }
    }

    /**
     * 同步设备流量
     * @param  \system\beans\sync\SyncBaseBeans $syncBaseBeans
     * date: 2022-06-01 10:09:06
     * @author  <mawei.live>
     * @return void
     */
    function syncDeviceFlowByDevno(SyncBaseBeans $syncBaseBeans)
    {
        // 提取同步设备流量
        $list = (new Query())->from(TableMap::TbDeviceFlow)
            ->where([
                'Devno'     => $syncBaseBeans->from_device_no,
                "sync_id" => 0,
            ])
            ->all($this->syncFromDB);
        if (count($list) < 1) {
            return 1;
        }
        $oldId = 0;
        foreach ($list as $v) {
            $v['Company_id'] = $syncBaseBeans->to_enterprise_id;
            $v['sync_id']    = $oldId = $v['id'];
            unset($v['Id']);
            // 插入数据
            $result = $this->syncToDB->createCommand()->insert(TableMap::TbDeviceFlow, $v)->execute();
            if ($result === false) {
                HelperFuns::writeLog("error:{$oldId}", "device.log", "insert new flowrecord");
                return -1;
            }
            $id = $this->syncToDB->getLastInsertID();

            // 更新同步记录
            $result = $this->syncFromDB->createCommand()->update(TableMap::TbDeviceFlow, ["sync_id" => $id], ['id' => $oldId])->execute();
            if ($result === false) {
                HelperFuns::writeLog("error:{$oldId}", "device.log", "update old flowrecord");
                return -3;
            }
        }

        return 1;
    }

    // 构造函数
    function __construct()
    {
        $this->syncFromDB = \Yii::$app->dbcenter;
        $this->syncToDB = \Yii::$app->dbcenter_to;
    }

    private $syncToDB;
    private $syncFromDB;
}
