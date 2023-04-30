<?php

/**
 * 设备，节目播放统计
 * @Author: MaWei 
 * @Date: 2023-04-26 23:00:31 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-05-09 15:13:02
 */

namespace system\services\statistic;

use yii\db\Query;
use system\common\{HelperFuns, TableMap, ServiceFactory, SrvConfig};
use system\beans\statistic\DevAdvStatBeans;

class DeviceAdvSrv
{

    /**
     * 设备统计
     * @param  \system\beans\statistic\DevAdvStatBeans $param
     * date: 2023-04-29 08:53:48
     * @author  <mawei.live>
     * @return array
     */
    function getDevList(DevAdvStatBeans $param)
    {
        $query = (new Query())->from(TableMap::DeviceAdvStatistic)
            ->groupBy("devno,adv_id");

        // 时间
        if ($param->stime && $param->etime) {
            $query->where([
                'and',
                ['>=', "date", $param->stime],
                ['<=', "date", $param->etime],
            ]);
        }

        // 设备
        if ($param->dev_no) {
            $query->where(['devno' => $param->dev_no]);
        }

        // 总条数
        $count = $query->count();
        // if ($count < 1) {
        //     return [];
        // }

        // 分页
        $param->page($count);

        // 提取
        $list = $query->orderBy("id desc")
            ->select("adv_id,devno,SUM(`play_num`) play_num,SUM(`play_time`) play_time")->limit($param->limit)
            ->offset($param->offset)
            ->all();

        return $list;
    }

    /**
     * 广告统计列表
     * @param  \system\beans\statistic\DevAdvStatBeans $param
     * date: 2023-04-29 08:55:26
     * @author  <mawei.live>
     * @return array
     */
    function getAdvList(DevAdvStatBeans $param)
    {
        $query = (new Query())->from(TableMap::AdvDeviceStatistic)
            ->groupBy("devno,adv_id");

        // 时间
        if ($param->stime && $param->etime) {
            $query->where([
                'and',
                ['>=', "date", $param->stime],
                ['<=', "date", $param->etime],
            ]);
        }

        // 广告id
        if ($param->adv_id) {
            $query->where(['adv_id' => $param->adv_id]);
        }

        // 总条数
        $count = $query->count();
        if ($count < 1) {
            return [];
        }

        // 分页
        $param->page($count);

        // 提取
        $list = $query->orderBy("id desc")
            ->select("adv_id,devno,SUM(`play_num`) play_num,SUM(`play_time`) play_time")->limit($param->limit)
            ->limit($param->limit)
            ->offset($param->offset)
            ->all();

        return $list;
    }

    /**
     * 设备信息
     * @param  array $_devNo
     * date: 2023-04-29 09:22:53
     * @author  <mawei.live>
     * @return array
     */
    function getDevInfoByNos($_devNo)
    {
        $field = "te.Fullname enterprise_name,te.Shortname enterprise_nike,td.`Name` device_name,td.Devno dev_no,td.DevType dev_type";
        return (new Query())->from(TableMap::TbDevice . ' as td')
            ->leftJoin(TableMap::TbEnterprise . ' as te', "te.id=td.Company_id")
            ->select($field)
            ->where(
                ["in", "td.Devno", $_devNo]
            )
            ->indexBy("dev_no")
            ->all(\Yii::$app->dbcenter);
    }

    /**
     * 节目信息
     * @param  array $_advIds
     * date: 2023-04-30 08:26:02
     * @author  <mawei.live>
     * @return array
     */
    function getAdvInfoByIds($_advIds)
    {
        return (new Query())->from(TableMap::TbMakeAddver)
            ->where(["in", "id", $_advIds])
            ->select("id adv_id,rname adv_name")
            ->indexBy("adv_id")
            ->all(\Yii::$app->dbcenter);
    }

    /**
     * 设备播放的节目，时长统计
     * date: 2023-04-28 20:27:04
     * @author  <mawei.live>
     * @return void
     */
    function cornDevAdvStatistic()
    {
        $day = date("Y-m-d", strtotime("-1 day"));
        $table = "tbPlayLog" . date("Ymd", strtotime("-1 day"));
        $list = (new Query())->from($table)
            ->select("`devno`,count(*) play_num,`tplid` adv_id,SUM(`end`-`start`) play_time")
            ->groupBy("devno,adv_id")
            ->all(\Yii::$app->dbdata);

        foreach ($list as $v) {
            $v['date'] = $day;
            $result = \Yii::$app->db->createCommand()->insert(TableMap::DeviceAdvStatistic, $v)->execute();
            if ($result === false) {
                HelperFuns::writeLog("error:" . json_encode($v), "advdevstatistic.log", "insert new cornDevAdvStatistic");
            }
        }
        echo 1;
    }

    /**
     * 节目在多少设备播放，时长统计
     * date: 2023-04-28 20:27:35
     * @author  <mawei.live>
     * @return void
     */
    function cornAdvDevStatistic()
    {
        $day = date("Y-m-d", strtotime("-1 day"));
        $table = "tbPlayLog" . date("Ymd", strtotime("-1 day"));
        $list = (new Query())->from($table)
            ->select("`tplid` adv_id,count(*) play_num,devno,SUM(`end`-`start`) play_time")
            ->groupBy("tplid,devno")
            ->all(\Yii::$app->dbdata);

        foreach ($list as $v) {
            $v['date'] = $day;
            $result = \Yii::$app->db->createCommand()->insert(TableMap::AdvDeviceStatistic, $v)->execute();
            if ($result === false) {
                HelperFuns::writeLog("error:" . json_encode($v), "advdevstatistic.log", "insert new cornAdvDevStatistic");
            }
        }
        echo 2;
    }
}
