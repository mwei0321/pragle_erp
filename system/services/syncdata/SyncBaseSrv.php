<?php

/* 
 * 同步I
 * @Author: MaWei 
 * @Date: 2022-06-19 18:51:35 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-06-19 18:59:57
 */

namespace system\services\syncdata;

class SyncBaseSrv
{

    // 构造函数
    function __construct()
    {
        $this->syncFromDB = \Yii::$app->dbcenter_from;
        $this->syncToDB = \Yii::$app->dbcenter_to;
    }
    public $syncToDB;
    public $syncFromDB;
}
