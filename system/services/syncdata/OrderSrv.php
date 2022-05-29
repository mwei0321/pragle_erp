<?php

/* 设备同步
 * @Author: MaWei 
 * @Date: 2022-05-22 10:22:20 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-05-29 16:57:50
 */

namespace system\services\syncdata;

use yii\db\Query;
use system\common\{TableMap, HelperFuns, ServiceFactory};
use system\beans\sync\SyncBaseBeans;

class OrderSrv
{

    /**
     * 根据uid同步订单
     * @param  \system\beans\sync\SyncBaseBeans $syncBaseBeans
     * date: 2022-05-28 16:51:24
     * @author  <mawei.live>
     * @return void
     */
    function syncOrderByUid(SyncBaseBeans $syncBaseBeans)
    {
        // 提取用户订单
        $list = (new Query())->from(TableMap::TbOrder)
            ->where([
                'uid'     => $syncBaseBeans->from_uid,
                "sync_id" => 0,
            ])
            ->all($this->syncFromDB);
        $oldId = 0;
        foreach ($list as $v) {
            $v['uid'] = $syncBaseBeans->to_uid;
            $v['Company_id'] = $syncBaseBeans->to_enterprise_id;
            $v['sync_id'] = $oldId = $v['order_id'];
            unset($v['order_id']);
            // 插入数据
            $result = $this->syncToDB->createCommand()->insert(TableMap::TbOrder, $v)->execute();
            if ($result === false) {
                HelperFuns::writeLog("error:{$oldId}", "syncOrder.log", "insert new order");
            }
            $id = $this->syncToDB->getLastInsertID();

            // 更新同步记录
            $result = $this->syncFromDB->createCommand()->update(TableMap::TbOrder, ["sync_id" => $id], ['order_id' => $oldId])->execute();
            if ($result === false) {
                HelperFuns::writeLog("error:{$oldId}", "syncOrder.log", "update old order");
            }

            // 更新order_item
            $itemInfo = (new Query())->from(TableMap::TbOrderItem)->where(['order_id' => $oldId])->one($this->syncFromDB);
            if ($itemInfo && isset($itemInfo['order_item_id'])) {
                $itemInfo['order_id'] = $id;
                $itemOldId = $itemInfo['order_item_id'];
                $itemInfo['sync_id'] = $itemOldId;
                unset($itemInfo['order_item_id']);
                $result = $this->syncToDB->createCommand()->insert(TableMap::TbOrderItem, $itemInfo)->execute();
                if ($result === false) {
                    HelperFuns::writeLog("error:{$itemOldId}", "syncOrder.log", "insert new orderItem");
                }
                $itemId = $this->syncToDB->getLastInsertID();
                // 更新同步记录
                $result = $this->syncFromDB->createCommand()->update(TableMap::TbOrderItem, ["sync_id" => $itemId], ['order_item_id' => $itemOldId])->execute();
                if ($result === false) {
                    HelperFuns::writeLog("error:{$itemOldId}", "syncOrder.log", "update old orderItem");
                }
            }

            // 更新order_log
            $orderLog = (new Query())->from(TableMap::TbOrderLog)->where(['order_id' => $oldId])->one($this->syncFromDB);
            if ($orderLog && isset($orderLog['log_id'])) {
                $orderLog['order_id'] = $id;
                $logOldId = $orderLog['log_id'];
                $orderLog['sync_id'] = $logOldId;
                unset($orderLog['log_id']);
                $result = $this->syncToDB->createCommand()->insert(TableMap::TbOrderLog, $orderLog)->execute();
                if ($result === false) {
                    HelperFuns::writeLog("error:{$logOldId}", "syncOrder.log", "insert new orderLog");
                }
                $itemId = $this->syncToDB->getLastInsertID();
                // 更新同步记录
                $result = $this->syncFromDB->createCommand()->update(TableMap::TbOrderLog, ["sync_id" => $itemId], ['log_id' => $logOldId])->execute();
                if ($result === false) {
                    HelperFuns::writeLog("error:{$logOldId}", "syncOrder.log", "update old orderLog");
                }
            }

            // 写入钱包记录
            $syncBaseBeans->from_order_id = $oldId;
            $syncBaseBeans->to_order_id   = $id;
            $result = ServiceFactory::getInstance("SyncWalletSrv")->syncWallLogByOrderId($syncBaseBeans);
        }
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
