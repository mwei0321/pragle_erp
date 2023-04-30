<?php

/* 积分
 * @Author: MaWei 
 * @Date: 2022-01-23 21:22:31 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-01-23 22:02:48
 */

namespace app\controllers\erpapi;

use app\controllers\InitController;
use system\common\{ServiceFactory, TableMap};
use system\traits\BindBeanParamsTrait;
use system\beans\statistic\DevAdvStatBeans;

class DevadvController extends InitController
{
    use BindBeanParamsTrait;

    /**
     * 设备
     * @param  DevAdvStatBeans $param
     * date: 2023-04-29 08:55:12
     * @author  <mawei.live>
     * @return array
     */
    function actionGetdev(DevAdvStatBeans $param)
    {
        // 提取列表
        $list = ServiceFactory::getInstance("DeviceAdvSrv")->getDevList($param);

        // 企业信息
        if (count($list) > 0) {
            // 广告
            $adv = array_column($list, "adv_id");
            $advInfo = ServiceFactory::getInstance("DeviceAdvSrv")->getAdvInfoByIds($adv);

            // 设备
            $default = ["enterprise_name" => '', "enterprise_nike" => '', "device_name" => "", "dev_type" => ""];
            $devNo = array_column($list, "devno");
            $devInfo = ServiceFactory::getInstance("DeviceAdvSrv")->getDevInfoByNos($devNo);
            foreach ($list as $k => &$v) {
                $list[$k] = array_merge($v, ($devInfo[$v['devno']] ?? $default));
                $list[$k] = array_merge($v, $advInfo[$v['adv_id']] ?? ['adv_name' => ""]);
            }
        }

        return $this->reJson($list);
    }

    /**
     * 广告
     * @param  DevAdvStatBeans $param
     * date: 2023-04-30 08:49:29
     * @author  <mawei.live>
     * @return array
     */
    function actionGetadv(DevAdvStatBeans $param)
    {
        // 提取列表
        $list = ServiceFactory::getInstance("DeviceAdvSrv")->getAdvList($param);

        if (count($list) > 0) {
            // 广告
            $adv = array_column($list, "adv_id");
            $advInfo = ServiceFactory::getInstance("DeviceAdvSrv")->getAdvInfoByIds($adv);

            // 设备
            $default = ["enterprise_name" => '', "enterprise_nike" => '', "device_name" => "", "dev_type" => ""];
            $devNo = array_column($list, "devno");
            $devInfo = ServiceFactory::getInstance("DeviceAdvSrv")->getDevInfoByNos($devNo);
            foreach ($list as $k => &$v) {
                $list[$k] = array_merge($v, ($devInfo[$v['devno']] ?? $default));
                $list[$k] = array_merge($v, ($advInfo[$v['adv_id']] ?? ['adv_name' => ""]));
            }
        }

        return $this->reJson($list);
    }
}
