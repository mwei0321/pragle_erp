<?php
/*
 * @Author: MaWei 1123265518@qq.com
 * @Date: 2022-06-21
 * @LastEditors: MaWei 1123265518@qq.com
 * @LastEditTime: 2022-06-29
 * @FilePath: \Pragle_erp\system\common\SrvMap.php
 * @Description: 服务地图
 * 
 * Copyright (c) 2022 by MaWei 1123265518@qq.com, All Rights Reserved. 
 */

namespace system\common;

class SrvMap
{
    // 数据基础
    public $BaseDB                = 'system\common\BaseDB';

    // service
    public $MaketingKpiSrv        = 'system\services\kpi\MaketingKpiServices';
    public $ActionKpiSrv          = 'system\services\kpi\ActionKpiServices';
    public $KpiRecordSrv          = 'system\services\kpi\KpiRecordServices';
    public $ActionLogSrv          = 'system\services\kpi\ActionLogServices';
    public $KpiGraphicSrv         = 'system\services\graphic\KpiGraphicServices';
    public $ScoreGraphiSrv        = 'system\services\graphic\ScroeGraphicServices';
    public $ActionFollowSrv       = 'system\services\follow\ActionFollowServices';
    public $ActionScoreSrv        = 'system\services\score\ActionScoreServices';
    public $ProductSrv            = 'system\services\product\ProductServices';
    public $OrderSrv              = 'system\services\order\OrderServices';
    public $DepartmentSrv         = 'system\services\user\DepartmentServices';
    public $ActionStatSrv         = 'system\services\statistic\ActionStatServices';
    public $ConsultSrv            = 'system\services\consult\ConsultServices';
    public $ProjectSrv            = 'system\services\project\ProjectServices';
    public $VideoSrv              = 'system\services\product\VideoServices';

    // 定时任务
    public $EmailCronSrv          = 'system\services\cron\EmailCronServices';
    public $ActionCronSrv         = 'system\services\cron\ActionCronServices';
    public $MarketCronSrv         = 'system\services\cron\MarketCronServices';
    public $FollowCronSrv         = 'system\services\cron\FollowCronServices';
    public $CustomerCronSrv       = 'system\services\cron\CustomerCronServices';
    public $OrderCronSrv          = 'system\services\cron\OrderCronServices';

    // 数据同步
    public $SyncEnterpriseUserSrv = "system\services\syncdata\EnterpriseUserSrv";
    public $SyncDeviceSrv         = "system\services\syncdata\DeviceSrv";
    public $SyncMaterialSrv       = "system\services\syncdata\MaterialSrv";
    public $SyncOrderSrv          = "system\services\syncdata\OrderSrv";
    public $SyncPlaySrv           = "system\services\syncdata\PlaySrv";
    public $SyncWalletSrv         = "system\services\syncdata\WalletSrv";
    public $SyncFlowSrv           = "system\services\syncdata\FlowSrv";


    // 引用旧的代码

    /**
     * 返回定义的成员属性列表
     * @return [type] [description]
     * @Date   2019-03-13T17:56:51+0800
     * @Author MaWei <1123265518@qq.com>
     * @Link   http://mawei.live
     */
    function toArray()
    {
        $ref = new \ReflectionClass(static::class);
        $propArr = $ref->getProperties();
        $attrArr = [];
        foreach ($propArr as $obj) {
            $name = $obj->getName();
            @$this->$name && $attrArr[$name] = $this->$name;
        }
        return $attrArr;
    }
}
