<?php

/**
 * 服务地图
 * @Author: MaWei
 * @Date:   2021-10-27
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-01-06 10:40:38 10:40:38
 */

namespace system\common;

class SrvMap
{
    // 数据基础
    public $BaseDB = 'system\common\BaseDB';

    // service
    public $MaketingKpiSrv  = 'system\services\kpi\MaketingKpiServices';
    public $ActionKpiSrv    = 'system\services\kpi\ActionKpiServices';
    public $KpiRecordSrv    = 'system\services\kpi\KpiRecordServices';
    public $KpiGraphicSrv   = 'system\services\graphic\KpiGraphicServices';
    public $ScoreGraphiSrv  = 'system\services\graphic\ScroeGraphicServices';
    public $ActionFollowSrv = 'system\services\follow\ActionFollowServices';
    public $FollowScoreSrv  = 'system\services\score\FollowScoreServices';


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
