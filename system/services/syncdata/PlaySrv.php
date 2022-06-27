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
        $jump = true;
        $offset = 0;
        $cnt = 0;
        while ($jump) {
            $offset = $cnt * 10;
            $advlist = (new Query())->from(TableMap::TbMakeAddver)->where(['uid' => $syncBaseBeans->from_uid])->orderBy("id asc")->offset($offset)->limit(100)->all($this->syncFromDB);
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
                        $this->syncFromDB->createCommand()->update(TableMap::TbMakeAddver, ["sync_id" => -1], ['id' => $old])->execute();
                        continue;
                    }
                    // 如果查询的素材和原素材数不相同,跳过
                    if (count($mIds) != count($mNewIds)) {
                        $this->syncFromDB->createCommand()->update(TableMap::TbMakeAddver, ["sync_id" => -2], ['id' => $old])->execute();
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
                }
            } else {
                $jump = false;
            }
            $cnt++;
            unset($advlist);
        }
        return $cnt;
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
        $jump = true;
        $offset = 0;
        $cnt = 0;
        while ($jump) {
            $offset = $cnt * 100;
            $playlist = (new Query())->from(TableMap::TbPlayList)->where(['Uid' => $syncBaseBeans->from_uid])->offset($offset)->limit(100)->all($this->syncFromDB);
            var_dump($playlist);
            if ($playlist) {
                foreach ($playlist as $v) {
                    $old             = $v['Pid'];
                    // 查询ad
                    $adinfo = (new Query())->from(TableMap::TbMakeAddver)->where(['id' => $v['Ad']])->select("sync_id")->one($this->syncFromDB);
                    if ($adinfo && isset($adinfo) && $adinfo['sync_id'] > 0) {
                        $syncBaseBeans->to_ad_id = $adinfo['sync_id'];
                    } else {
                        $this->syncFromDB->createCommand()->update(TableMap::TbPlayList, ["sync_id" => -2], ['Pid' => $old])->execute();
                        continue;
                    }

                    $v['sync_id']    = $old;
                    $v['uid']        = $syncBaseBeans->to_uid;
                    $v['Company_id'] = $syncBaseBeans->to_enterprise_id;
                    $v['Ad']         = $syncBaseBeans->to_ad_id;
                    $syncBaseBeans->from_play_id = $old;
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

                    // 同步播放bae
                    $this->syncPlayBaseByPlayId($syncBaseBeans);
                }
            } else {
                $jump = false;
            }
            $cnt++;
        }
        return 1;
    }

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

    /**
     * Undocumented function
     * @param  \system\beans\sync\SyncBaseBeans $syncBaseBeans
     * date: 2022-06-24 23:01:21
     * @author  <mawei.live>
     * @return void
     */
    function syncPlayBaseByPlayId(SyncBaseBeans $syncBaseBeans)
    {
        $playlist = (new Query())->from(TableMap::TbPlayBase)->where(['Playlist_id' => $syncBaseBeans->from_play_id])->all($this->syncFromDB);
        if ($playlist) {
            foreach ($playlist as $k => $v) {
                $old              = $v['Pid'];
                $v['sync_id']     = $old;
                $v['Playlist_id'] = $syncBaseBeans->to_play_id;
                $v['Ad']          = $syncBaseBeans->to_ad_id;
                unset($v['Pid']);
                // 插入数据
                $result = $this->syncToDB->createCommand()->insert(TableMap::TbPlayBase, $v)->execute();
                if ($result === false) {
                    HelperFuns::writeLog("error_palylan_id:" . $syncBaseBeans->to_play_id, "syncplay.log", "insert new palylan");
                    return -1;
                }
                $newId = $this->syncToDB->getLastInsertID();
                // 更新同步记录
                $result = $this->syncFromDB->createCommand()->update(TableMap::TbPlayBase, ["sync_id" => $newId], ['Pid' => $old])->execute();
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
