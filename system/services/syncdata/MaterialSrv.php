<?php
/*
 * @Author: MaWei 1123265518@qq.com
 * @Date: 2022-05-23 09:08:49
 * @LastEditors: MaWei 1123265518@qq.com
 * @LastEditTime: 2022-05-23 09:46:23
 * @FilePath: \pragle_erp\system\services\syncdata\MaterialSrv.php
 * @Description: 素材同步
 * 
 * Copyright (c) 2022 by MaWei 1123265518@qq.com, All Rights Reserved. 
 */

namespace system\services\syncdata;

use \Yii;
use yii\db\Query;
use system\common\TableMap;
use system\beans\sync\SyncBaseBeans;
use system\common\HelperFuns;

class MaterialSrv
{
    /**
     * 同步video素材
     * @param  \system\beans\sync\SyncBaseBeans $syncBase
     * date: 2022-05-28 16:44:19
     * @author  <mawei.live>
     * @return void
     */
    function syncVideoByUid(SyncBaseBeans $syncBase)
    {
        $list = (new Query())->from(TableMap::TbVedio)
            ->where([
                'uid'     => $syncBase->from_uid,
                "is_sync" => 0,
                "Delete"  => 0,
            ])
            ->all($this->syncFromDB);

        $oldId = 0;
        foreach ($list as $v) {
            $v['uid'] = $syncBase->to_uid;
            $v['Company_id'] = $syncBase->to_enterprise_id;
            $v['sync_id'] = $oldId = $v['Vid'];
            unset($v['Vid']);
            // 插入数据
            $result = $this->syncToDB->createCommand()->insert(TableMap::TbVedio, $v)->execute();
            if ($result === false) {
                HelperFuns::writeLog("error:{$oldId}", "syncMaterial.log", "insert new video");
            }
            $id = $this->syncToDB->getLastInsertID();
            // 更新同步记录
            $result = $this->syncFromDB->createCommand()->update(TableMap::TbVedio, ["sync_id" => $id, ['Vid' => $oldId]])->execute();
            if ($result === false) {
                HelperFuns::writeLog("error:{$oldId}", "syncMaterial.log", "update old video");
            }
        }
    }

    /**
     * 同步analysis 记录
     * @param  \system\beans\sync\SyncBaseBeans $syncBase
     * date: 2022-05-28 16:44:43
     * @author  <mawei.live>
     * @return void
     */
    function syncAnalysisByUid(SyncBaseBeans $syncBase)
    {
        $list = (new Query())->from(TableMap::TbAnalysis)
            ->where([
                'uid'     => $syncBase->from_uid,
                "is_sync" => 0,
            ])
            ->all($this->syncFromDB);

        $oldId = 0;
        foreach ($list as $v) {
            $v['uid'] = $syncBase->to_uid;
            $v['Company_id'] = $syncBase->to_enterprise_id;
            $v['sync_id'] = $oldId = $v['Vid'];
            unset($v['Vid']);
            // 插入数据
            $result = $this->syncToDB->createCommand()->insert(TableMap::TbAnalysis, $v)->execute();
            if ($result === false) {
                HelperFuns::writeLog("error:{$oldId}", "syncMaterial.log", "insert new analysis");
            }
            $id = $this->syncToDB->getLastInsertID();
            // 更新同步记录
            $result = $this->syncFromDB->createCommand()->update(TableMap::TbAnalysis, ["sync_id" => $id, ['id' => $oldId]])->execute();
            if ($result === false) {
                HelperFuns::writeLog("error:{$oldId}", "syncMaterial.log", "update old analysis");
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
