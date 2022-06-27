<?php
/*
 * @Author: MaWei 1123265518@qq.com
 * @Date: 2022-05-23 09:08:49
 * @LastEditors: MaWei 1123265518@qq.com
 * @LastEditTime: 2022-06-24
 * @FilePath: \Pragle_erp\system\services\syncdata\MaterialSrv.php
 * @Description: 素材同步
 * 
 * Copyright (c) 2022 by MaWei 1123265518@qq.com, All Rights Reserved. 
 */

namespace system\services\syncdata;

use yii\db\Query;
use system\common\TableMap;
use system\beans\sync\SyncBaseBeans;
use system\common\HelperFuns;
use system\services\syncdata\SyncBaseSrv;

class MaterialSrv extends SyncBaseSrv
{
    /**
     * 同步video素材
     * @param  \system\beans\sync\SyncBaseBeans $syncBaseBeans
     * date: 2022-05-28 16:44:19
     * @author  <mawei.live>
     * @return void
     */
    function syncVideoByUid(SyncBaseBeans $syncBaseBeans)
    {
        $list = (new Query())->from(TableMap::TbVedio)
            ->where([
                'uid'     => $syncBaseBeans->from_uid,
            ])
            ->all($this->syncFromDB);
        if (count($list) < 1) {
            return 1;
        }
        $oldId = 0;
        foreach ($list as $v) {
            $v['uid']        = $syncBaseBeans->to_uid;
            $v['Company_id'] = $syncBaseBeans->to_enterprise_id;
            $v['CpID']       = $syncBaseBeans->to_parent_enterpirse;
            $v['sync_id']    = $oldId = $v['Vid'];
            unset($v['Vid']);
            // 插入数据
            $result = $this->syncToDB->createCommand()->insert(TableMap::TbVedio, $v)->execute();
            if ($result === false) {
                HelperFuns::writeLog("error:{$oldId}", "syncMaterial.log", "insert new video");
                return -1;
            }
            $id = $this->syncToDB->getLastInsertID();
            // 更新同步记录
            $result = $this->syncFromDB->createCommand()->update(TableMap::TbVedio, ["sync_id" => $id], ['Vid' => $oldId])->execute();
            if ($result === false) {
                HelperFuns::writeLog("error:{$oldId}", "syncMaterial.log", "update old video");
                return -2;
            }
        }

        return 1;
    }

    /**
     * 同步analysis 记录
     * @param  \system\beans\sync\SyncBaseBeans $syncBaseBeans
     * date: 2022-05-28 16:44:43
     * @author  <mawei.live>
     * @return void
     */
    function syncAnalysisByUid(SyncBaseBeans $syncBaseBeans)
    {
        $list = (new Query())->from(TableMap::TbAnalysis)
            ->where([
                'uid'     => $syncBaseBeans->from_uid,
            ])
            ->all($this->syncFromDB);
        if (count($list) < 1) {
            return 1;
        }
        $oldId = 0;
        foreach ($list as $v) {
            $v['uid']        = $syncBaseBeans->to_uid;
            $v['Company_id'] = $syncBaseBeans->to_enterprise_id;
            $v['CpID']       = $syncBaseBeans->to_parent_enterpirse;
            $v['sync_id']    = $oldId = $v['id'];
            unset($v['id']);
            // 插入数据
            $result = $this->syncToDB->createCommand()->insert(TableMap::TbAnalysis, $v)->execute();
            if ($result === false) {
                HelperFuns::writeLog("error:{$oldId}", "syncMaterial.log", "insert new analysis");
                return -1;
            }
            $id = $this->syncToDB->getLastInsertID();
            // 更新同步记录
            $result = $this->syncFromDB->createCommand()->update(TableMap::TbAnalysis, ["sync_id" => $id], ['id' => $oldId])->execute();
            if ($result === false) {
                HelperFuns::writeLog("error:{$oldId}", "syncMaterial.log", "update old analysis");
                return -3;
            }
        }

        return 1;
    }

    /**
     * 根据旧的素材id返回新的id
     * @param  array $_oldMaterialIds
     * date: 2022-06-19 18:12:55
     * @author  <mawei.live>
     * @return array
     */
    function getNewMaterialIdsByOldIds($_oldMaterialIds)
    {
        $data = [];
        $ids = (new Query())->from(TableMap::TbVedio)->select('Vid,sync_id')
            ->where(["in", "Vid", $_oldMaterialIds])
            ->all($this->syncFromDB);
        if ($ids) {
            $data = array_combine(array_column($ids, "Vid"), array_column($ids, "sync_id"));
        }

        return $data;
    }

    // 构造函数
    function __construct()
    {
        parent::__construct();
    }
}
