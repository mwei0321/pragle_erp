<?php
/*
 * @Author: MaWei 1123265518@qq.com
 * @Date: 2022-05-23 09:08:49
 * @LastEditors: MaWei 1123265518@qq.com
 * @LastEditTime: 2022-05-23 09:46:23
 * @FilePath: \pragle_erp\system\services\syncdata\MaterialSrv.php
 * @Description: 素材同步
 * 
 * Copyright (c) 2022 by MaWei 1123265518@qq.com, All Rights Reserved. 
 */

namespace system\services\syncdata;

use \Yii;
use yii\db\Query;
use system\common\TableMap;
use system\beans\sync\SyncBaseBeans;

class MaterialSrv
{

    function syncMaterial()
    {
        $list = (new Query())->from(TableMap::TbVedio)
            ->where(["is_sync" => 0])
            ->limit(2)
            ->all();
    }


    function syncMaterialByUid(SyncBaseBeans $syncBase)
    {
        $list = (new Query())->from(TableMap::TbVedio)
            ->where(['uid' => $syncBase->from_uid])
            ->all();
    }
}
