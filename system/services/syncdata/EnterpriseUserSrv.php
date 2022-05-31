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
     * 同步企业用户
     * 查询A库TbUser表里面的email在B库的TbUser的email是否存在,如果存在,不同步企业和企业的用户员工
     * date: 2022-05-21 20:31:56
     * @author  <mawei.live>
     * @return void
     */
    function syncEnterpriseUser()
    {
        // 提取未同步企业id列表
        $enterpirseList = (new Query())->from(TableMap::TbUser . " AS u")
            ->leftJoin(TableMap::TbUserInfo . " AS ui", "ui.uid = u.uid")
            ->select("u.Company_id cy_id,GROUP_CONCAT(u.`uid`) `uid`,GROUP_CONCAT(`ui`.`Email`) `email`")
            ->where([
                "u.is_sync" => 0,
            ])
            ->groupBy("u.Company_id")
            ->orderBy("cy_id ASC")
            ->limit(2)
            ->all($this->syncFromDB);
        // 开启事务
        $connection = $this->syncFromDB->beginTransaction();

        // 查询企业员工是否存在,存在跳过,不同步
        foreach ($enterpirseList as $val) {
            if (!$val['email']) {
                continue;
            }
            $uidArr = explode(",", $val['uid']);
            $emailArr = explode(",", $val['email']);

            // 查询是否存在
            $isExist = $this->checkEmailIsExist($emailArr);
            if ($isExist) {
                // 更新数据为不需求同步
                $this->updateSyncState($uidArr, $val['cy_id']);
                // 跳过,不同步
                continue;
            }

            // 不存在,同步数据
            $enterpriseId = $this->syncEnterprise($val['cy_id']);

            // 同步企业
            if ($enterpriseId < 1) {
                //失败回滚
                $connection->rollback();
                continue;
            }
            // 同步企业员工信息
            if ($this->syncUserInfo($uidArr, $enterpriseId) < 1) {
                //失败回滚
                $connection->rollback();
                continue;
            }

            // 更新同步成功状态
            $this->updateSyncState($uidArr, $val['cy_id']);

            // 提交事务
            $connection->commit();
            exit;
        }

        // 提交事务
        $connection->commit();

        return true;
    }



    /**
     * 同步企业
     * date: 2022-05-22 00:36:39
     * @author  <mawei.live>
     * @return void
     */
    function syncEnterprise(SyncBaseBeans $syncBaseBeans)
    {
        // 查询信息
        $info = (new Query())->from(TableMap::TbEnterprise)
            ->where([
                'id'      => $syncBaseBeans->from_enterprise_id,
                "sync_id" => 0,
            ])->one($this->syncFromDB);
        if (!$info || isset($info['id'])) {
            return -1;
        }

        $info['sync_id'] = $oldId = $info['id'];
        unset($info['id']);
        unset($info['auto_power_off']);
        unset($info['is_sync']);

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
    function syncUserInfo(SyncBaseBeans $syncBaseBeans)
    {
        // 查询企业员工
        $userList = (new Query())->from(TableMap::TbUser)
            ->where([
                'Company_id' => $syncBaseBeans->from_enterprise_id,
                "sync_id"    => 0,
            ])
            ->all($this->syncFromDB);
        foreach ($userList as $val) {
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
     * 更新同步状态
     * date: 2022-05-22 00:22:41
     * @author  <mawei.live>
     * @return void
     */
    function updateSyncState($_uidArr, $_enterpriseId, $_state = 1)
    {
        // 开启事务
        $connection = $this->syncToDB->beginTransaction();

        // 更新企业表
        $isSuccess = $this->syncFromDB->createCommand()
            ->update(TableMap::TbEnterprise, ["is_sync" => $_state], ['id' => $_enterpriseId])
            ->execute();
        if ($isSuccess === false) {
            //失败回滚
            $connection->rollback();
            return false;
        }

        // 更新企业员工详情表
        $isSuccess = $this->syncFromDB->createCommand()
            ->update(TableMap::TbUserInfo, ["is_sync" => $_state], ['uid' => $_uidArr])
            ->execute();
        if ($isSuccess === false) {
            //失败回滚
            $connection->rollback();
            return false;
        }

        // 更新企业员工表
        $isSuccess = $this->syncFromDB->createCommand()
            ->update(TableMap::TbUser, ["is_sync" => $_state], ['uid' => $_uidArr])
            ->execute();
        if ($isSuccess === false) {
            //失败回滚
            $connection->rollback();
            return false;
        }

        // 提交事务
        $connection->commit();

        return true;
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
