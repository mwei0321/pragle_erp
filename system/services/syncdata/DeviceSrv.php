<?php

/* 设备同步
 * @Author: MaWei 
 * @Date: 2022-05-22 10:22:20 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-05-22 12:51:09
 */

namespace system\services\syncdata;

use system\common\TableMap;
use yii\db\Query;
use \Yii;

class DeviceSrv
{

    /**
     * 同步设备信息
     * date: 2022-05-22 10:49:48
     * @author  <mawei.live>
     * @return void
     */
    function syncDeviceInfo()
    {
        // 提取设备列表
        $list = (new Query())->from(TableMap::TbDevice)
            ->select("Did,uid,CpID,Devno,Company_id")
            ->where([
                "is_sync" => 0,
            ])
            ->limit(2)
            ->all();

        // 提取
        $devSn = array_column($list, 'Devno');
        // 过滤设备
        $existList = $this->checkDevSnIsExsit($devSn);

        // 开启事务
        $connectionTo = $this->syncToDB->beginTransaction();

        // 处理设备是否同步
        $syncDevSn = [];
        foreach ($list as $val) {
            // 存在跳过
            if (in_array($val['Devno'], $existList)) {
                continue;
            }

            // 同步设备信息 
            $syncDevSn[] = $val['Devno'];
            // 提取设备信息
            $devInfo = (new Query())->from(TableMap::TbDevice)->where(['Did' => $val['Did']])->one($this->syncFromDB);
            $devInfo['sync_id'] = $devInfo['Did'];
            unset($devInfo['Did']);
            $deviceId = $this->syncToDB->createCommand()
                ->insert(TableMap::TbDevice, $devInfo)
                ->execute();
            if ($deviceId === false) {
                return false;
            }
            // 同步deviceflow
            $devFlowInfo = (new Query())->from(TableMap::TbDeviceFlow)->where(['Devno' => $val['Devno']])->all($this->syncFromDB);
            foreach ($devFlowInfo as $v) {
            }
        }

        // 更新不需要更新的设备
        $result = $this->updateDeviceState($devSn, -1);
        if (!$result) {
            //失败回滚
            return false;
        }
    }



    /**
     * 更新设备状态
     * @param  array  $_devSnArr
     * @param  integer $_state
     * date: 2022-05-22 11:56:53
     * @author  <mawei.live>
     * @return void
     */
    function updateDeviceState($_devSnArr, $_state = 1)
    {
        // 更新状态 
        $isSuccess = $this->syncFromDB->createCommand()
            ->update(TableMap::TbUserInfo, ["is_sync" => $_state], ['Devno' => $_devSnArr])
            ->execute();
        if ($isSuccess === false) {
            return false;
        }

        return true;
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
        $this->syncFromDB = \Yii::$app->dbcenter;
        $this->syncToDB = \Yii::$app->dbcenter_to;
    }

    private $syncToDB;
    private $syncFromDB;
}
