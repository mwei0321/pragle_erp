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

class WalletSrv
{
    /**
     * 根据uid同步钱包
     * @param \system\beans\sync\SyncBaseBeans $syncBaseBeans
     * date: 2022-05-28 16:51:24
     * @author <mawei.live>
     * @return void
     */
    function syncWallByUid(SyncBaseBeans $syncBaseBeans)
    {
        // 提取用户钱包
        $list = (new Query())->from(TableMap::TbWallet)
            ->where([
                'uid' => $syncBaseBeans->from_uid,
                "sync_id" => 0,
            ])
            ->all($this->syncFromDB);
        $oldId = 0;
        foreach ($list as $v) {
            $v['uid'] = $syncBaseBeans->to_uid;
            $v['Company_id'] = $syncBaseBeans->to_enterprise_id;
            $v['sync_id'] = $oldId = $v['wallet_id'];
            unset($v['wallet_id']);
            // 插入数据
            $result = $this->syncToDB->createCommand()->insert(TableMap::TbWallet, $v)->execute();
            if ($result === false) {
                HelperFuns::writeLog("error:{$oldId}", "syncOrder.log", "insert new order");
            }
            $id = $this->syncToDB->getLastInsertID();

            // 更新同步记录
            $result = $this->syncFromDB->createCommand()->update(TableMap::TbWallet, ["sync_id" => $id], ['wallet_id' => $oldId])->execute();
            if ($result === false) {
                HelperFuns::writeLog("error:{$oldId}", "syncOrder.log", "update old order");
            }
        }
    }

    /**
     * 同步钱包订单记录
     * @param  [type] $_orderId
     * date: 2022-05-29 16:08:23
     * @author  <mawei.live>
     * @return void
     */
    function syncWallLogByOrderId(SyncBaseBeans $syncBaseBeans)
    {
        $walletLogInfo = (new Query())->from(TableMap::TbWalletLog)->where(['order_id' => $syncBaseBeans->from_order_id])->one($this->syncFromDB);
        if ($walletLogInfo && isset($walletLogInfo['wallet_log_id'])) {
            $logOldId                    = $walletLogInfo['wallet_log_id'];
            $walletLogInfo['sync_id']    = $logOldId;
            $walletLogInfo['uid']        = $syncBaseBeans->to_uid;
            $walletLogInfo['order_id']   = $syncBaseBeans->to_order_id;
            $walletLogInfo['Company_id'] = $syncBaseBeans->to_enterprise_id;
            unset($walletLogInfo['wallet_log_id']);
            // 插入数据
            $result = $this->syncToDB->createCommand()->insert(TableMap::TbWalletLog, $walletLogInfo)->execute();
            if ($result === false) {
                HelperFuns::writeLog("error_order_id:" . $syncBaseBeans->to_order_id, "syncOrder.log", "insert new walletlog");
            }
            $logId = $this->syncToDB->getLastInsertID();
            // 更新同步记录
            $result = $this->syncFromDB->createCommand()->update(TableMap::TbWalletLog, ["sync_id" => $logId], ['wallet_log_id' => $logOldId])->execute();
            if ($result === false) {
                HelperFuns::writeLog("error_order_id" . $logId, "syncOrder.log", "update old order");
            }
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
