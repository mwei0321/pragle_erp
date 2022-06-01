<?php

/**
 * 同步企业用户
 * @Author: MaWei 
 * @Date: 2022-04-09 20:11:22 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-05-22 11:53:58
 */

namespace system\services\syncdata;

use system\beans\sync\SyncBaseBeans;
use system\common\TableMap;
use yii\db\Query;
use \Yii;

class EnterpriseUserSrv
{

    /**
     * 同步企业
     * date: 2022-05-22 00:36:39
     * @author  <mawei.live>
     * @return void
     */
    function syncEnterpriseById(SyncBaseBeans $syncBaseBeans)
    {
        // 查询信息
        $info = (new Query())->from(TableMap::TbEnterprise)
            ->where([
                'id'      => $syncBaseBeans->from_enterprise_id,
            ])->one($this->syncFromDB);
        if (!$info || !isset($info['id'])) {
            return -1;
        }

        $info['sync_id'] = $oldId = $info['id'];
        unset($info['id']);
        unset($info['auto_power_off']);
        // 待确认
        unset($info['user_del_type']);

        // 同步信息
        $result = $this->syncToDB->createCommand()->insert(TableMap::TbEnterprise, $info)->execute();
        if ($result === false) {
            return -2;
        }
        //返回ID
        $newId = $this->syncToDB->getLastInsertID();
        // 同步回写
        if ($this->syncFromDB->createCommand()->update(TableMap::TbEnterprise, ["sync_id" => $newId], ['id' => $oldId])->execute() === false) {
            return -3;
        }

        $syncBaseBeans->to_enterprise_id = $newId;

        return 1;
    }

    /**
     * 同步用户信息
     * @param  array $_uidArr
     * @param  int $_enterpriseId
     * date: 2022-05-22 00:53:54
     * @author  <mawei.live>
     * @return bool
     */
    function syncUserInfoById(SyncBaseBeans $syncBaseBeans)
    {
        // 查询企业员工
        $list = (new Query())->from(TableMap::TbUser)
            ->where([
                'uid' => $syncBaseBeans->from_uid,
            ])
            ->one($this->syncFromDB);
        if (count($list) < 1) {
            return 1;
        }
        foreach ($list as $val) {
            $oldUid = 0;
            $val['Company_id'] = $syncBaseBeans->to_enterprise_id;
            $val['sync_id']  = $oldUid  = $val['uid'];
            unset($val['uid']);
            $result = $this->syncToDB->createCommand()
                ->insert(TableMap::TbUser, $val)
                ->execute();
            if ($result === false) {
                return -1;
            }
            //返回ID
            $newUid = $this->syncToDB->getLastInsertID();
            // 更新同步id
            $result = $this->syncFromDB->createCommand()->update(TableMap::TbUser, ["sync_id" => $newUid], ['uid' => $oldUid])->execute();
            if ($result === false) {
                return -2;
            }

            // 查询企业员工详情
            $userDetail = (new Query())->from(TableMap::TbUserInfo)->where(['uid' => $val])->one($this->syncFromDB);
            $userDetail['uid']        = $newUid;
            $userDetail['sync_id']  = $oldId  = $userDetail['id'];
            unset($userDetail['id']);
            // 同步企业员工详情
            $result = $this->syncToDB->createCommand()
                ->insert(TableMap::TbUserInfo, $userDetail)
                ->execute();
            if ($result === false) {
                return -3;
            }
            $newUid = $this->syncToDB->getLastInsertID();

            // 更新同步
            $result = $this->syncFromDB->createCommand()->update(TableMap::TbUserInfo, ["sync_id" => $newUid], ['uid' => $oldId])->execute();
            if ($result === false) {
                return -4;
            }
        }

        return 1;
    }

    /**
     * 同步用户信息
     * @param  array $_uidArr
     * @param  int $_enterpriseId
     * date: 2022-05-22 00:53:54
     * @author  <mawei.live>
     * @return bool
     */
    function getEnterpriseSyncUserId(SyncBaseBeans $syncBaseBeans)
    {
        // 查询企业员工
        $list = (new Query())->from(TableMap::TbUser)
            ->select("uid")
            ->where([
                'Company_id' => $syncBaseBeans->from_enterprise_id,
                "sync_id"    => 0,
            ])
            ->all($this->syncFromDB);

        return $list ? array_column($list, "uid") : [];
    }

    /**
     * 同步统计记录
     * @param  \system\beans\sync\SyncBaseBeans $syncBaseBeans
     * date: 2022-06-01 11:15:47
     * @author  <mawei.live>
     * @return void
     */
    function syncUserActiveByEnterpriseId(SyncBaseBeans $syncBaseBeans)
    {
        // 查询信息
        $info = (new Query())->from(TableMap::UserActive)
            ->where([
                'company_id'      => $syncBaseBeans->from_enterprise_id,
            ])->one($this->syncFromDB);
        if (!$info || isset($info['id'])) {
            return -1;
        }
        $info['sync_id'] = $oldId = $info['id'];
        unset($info['id']);

        // 同步信息
        $result = $this->syncToDB->createCommand()->insert(TableMap::UserActive, $info)->execute();
        if ($result === false) {
            return -2;
        }
        //返回ID
        $newId = $this->syncToDB->getLastInsertID();

        // 同步回写
        if ($this->syncFromDB->createCommand()->update(TableMap::UserActive, ["sync_id" => $newId], ['id' => $oldId])->execute() === false) {
            return -3;
        }

        return 1;
    }

    /**
     * 查询email是否存在
     * @param  array $_emailArr
     * date: 2022-05-22 00:11:38
     * @author  <mawei.live>
     * @return bool
     */
    function checkEmailIsExist($_emailArr)
    {
        $info = (new Query())->from(TableMap::TbUserInfo)
            ->select("uid")
            ->where([
                "in", "Email", $_emailArr
            ])
            ->one($this->syncToDB);

        return isset($info['uid']) && $info['uid'] > 0 ? true : false;
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
