<?php

/* 节目同步
 * @Author: MaWei 
 * @Date: 2022-06-19 18:51:13 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-06-19 22:08:32
 */

namespace system\services\syncdata;

use yii\db\Query;
use system\common\{TableMap, HelperFuns, ServiceFactory};
use system\beans\sync\SyncBaseBeans;
use system\services\syncdata\SyncBaseSrv;

class PlaySrv extends SyncBaseSrv
{

    /**
     * 同步节目
     * @param  \system\beans\sync\SyncBaseBeans $syncBaseBeans
     * date: 2022-06-19 18:04:12
     * @author  <mawei.live>
     * @return void
     */
    function syncAddverByUId(SyncBaseBeans $syncBaseBeans)
    {
        $advlist = (new Query())->from(TableMap::TbMakeAddver)->where(['uid' => $syncBaseBeans->from_uid])->orderBy("id desc")->all($this->syncFromDB);
        if ($advlist) {
            foreach ($advlist as $v) {
                $old                       = $v['id'];
                $v['sync_id']              = $old;
                $v['uid']                  = $syncBaseBeans->to_uid;
                $v['Company_id']           = $syncBaseBeans->to_enterprise_id;
                $v["CpID"]                 = $syncBaseBeans->to_parent_enterpirse;
                $syncBaseBeans->from_ad_id = $old;
                unset($v['id']);
                // 素材id转数组
                $mIds = explode('","', substr($v['media_id'], 2, -2));
                // 处理素材
                $mIds = array_unique($mIds);
                if (!$mIds) {
                    continue;
                }
                // 提取新的ids
                $mNewIds = ServiceFactory::getInstance("SyncMaterialSrv")->getNewMaterialIdsByOldIds($mIds);
                if (!$mNewIds) {
                    continue;
                }
                // 如果查询的素材和原素材数不相同,跳过
                if (count($mIds) != count($mNewIds)) {
                    continue;
                }

                // 新的素材
                $newIdsStr = str_replace($mIds, array_values($mNewIds), $v['media_id']);
                $v['media_id'] = $newIdsStr;
                // 插入数据
                $result = $this->syncToDB->createCommand()->insert(TableMap::TbMakeAddver, $v)->execute();
                if ($result === false) {
                    HelperFuns::writeLog("error_addver_id:" . $syncBaseBeans->to_order_id, "syncplay.log", "insert new addver");
                    return -1;
                }
                $newId = $this->syncToDB->getLastInsertID();
                // 更新同步记录
                $result = $this->syncFromDB->createCommand()->update(TableMap::TbMakeAddver, ["sync_id" => $newId], ['id' => $old])->execute();
                if ($result === false) {
                    HelperFuns::writeLog("error_addver_id" . $newId, "syncplay.log", "update old addver");
                    return -2;
                }
                $syncBaseBeans->to_ad_id = $newId;

                // 同步节目列表
                $this->syncPlayListByAdId($syncBaseBeans);
            }

            return 1;
        }
    }

    /**
     * 同步播放列表
     * @param  \system\beans\sync\SyncBaseBeans $syncBaseBeans
     * date: 2022-06-19 18:03:56
     * @author  <mawei.live>
     * @return void
     */
    function syncPlayListByAdId(SyncBaseBeans $syncBaseBeans)
    {
        $playlist = (new Query())->from(TableMap::TbPlayList)->where(['Ad' => $syncBaseBeans->from_ad_id])->all($this->syncFromDB);
        if ($playlist) {
            foreach ($playlist as $k => $v) {
                $old             = $v['Pid'];
                $v['sync_id']    = $old;
                $v['uid']        = $syncBaseBeans->to_uid;
                $v['Company_id'] = $syncBaseBeans->to_enterprise_id;
                $v['Ad']         = $syncBaseBeans->to_ad_id;
                $syncBaseBeans->from_paly_id = $old;
                unset($v['Pid']);
                // 插入数据
                $result = $this->syncToDB->createCommand()->insert(TableMap::TbPlayList, $v)->execute();
                if ($result === false) {
                    HelperFuns::writeLog("error_paly_id:" . $syncBaseBeans->to_ad_id, "syncplay.log", "insert new playlist");
                    return -1;
                }
                $newId = $this->syncToDB->getLastInsertID();
                // 更新同步记录
                $result = $this->syncFromDB->createCommand()->update(TableMap::TbPlayList, ["sync_id" => $newId], ['Pid' => $old])->execute();
                if ($result === false) {
                    HelperFuns::writeLog("error_paly_id" . $newId, "syncplay.log", "update old playlist");
                    return -2;
                }
                $syncBaseBeans->to_play_id = $newId;

                // 同步列表播放计划
                $this->syncPlayListPlanByPlayId($syncBaseBeans);
            }

            return 1;
        }
    }

    /**
     * 同步播放列表计划
     * @param  \system\beans\sync\SyncBaseBeans $syncBaseBeans
     * date: 2022-06-19 21:33:40
     * @author  <mawei.live>
     * @return void
     */
    function syncPlayListPlanByPlayId(SyncBaseBeans $syncBaseBeans)
    {
        $playlist = (new Query())->from(TableMap::TbPlayListPlan)->where(['Playlist_id' => $syncBaseBeans->from_play_id])->all($this->syncFromDB);
        if ($playlist) {
            foreach ($playlist as $k => $v) {
                $old              = $v['Pid'];
                $v['sync_id']     = $old;
                $v['Playlist_id'] = $syncBaseBeans->to_play_id;
                $v['Ad']          = $syncBaseBeans->to_ad_id;
                unset($v['Pid']);
                // 插入数据
                $result = $this->syncToDB->createCommand()->insert(TableMap::TbPlayListPlan, $v)->execute();
                if ($result === false) {
                    HelperFuns::writeLog("error_palylan_id:" . $syncBaseBeans->to_play_id, "syncplay.log", "insert new palylan");
                    return -1;
                }
                $newId = $this->syncToDB->getLastInsertID();
                // 更新同步记录
                $result = $this->syncFromDB->createCommand()->update(TableMap::TbPlayListPlan, ["sync_id" => $newId], ['Pid' => $old])->execute();
                if ($result === false) {
                    HelperFuns::writeLog("error_palylan_id" . $newId, "syncplay.log", "update old palylan");
                    return -2;
                }
            }

            return 1;
        }
    }

    // 构造函数
    function __construct()
    {
        parent::__construct();
    }
}
