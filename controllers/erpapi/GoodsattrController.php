<?php

/*
 * @Author: MaWei 1123265518@qq.com
 * @Date: 2022-07-04
 * @LastEditors: MaWei 1123265518@qq.com
 * @LastEditTime: 2022-07-11
 * @FilePath: \Pragle_erp\controllers\erpapi\GoodsattrController.php
 * @Description: 商品属性
 * 
 * Copyright (c) 2022 by MaWei 1123265518@qq.com, All Rights Reserved. 
 */

namespace app\controllers\erpapi;

use app\controllers\InitController;
use system\common\{ServiceFactory, TableMap};
use system\traits\BindBeanParamsTrait;
use system\beans\goods\GoodsAttrBeans;
use yii\db\Query;

class FollowController extends InitController
{
    use BindBeanParamsTrait;
    function actionList(GoodsAttrBeans $goodsAttrBeans)
    {
        $query = (new Query())->from(TableMap::GoodsAttr)
            ->select("")
            ->where("1", 1);
    }

    function actionUpdate(GoodsAttrBeans $goodsAttrParams)
    {
        // 初始化数据库
        $dbObj = ServiceFactory::getInstance('BaseDB', TableMap::GoodsAttr);

        // 转存数据
        $data = $goodsAttrParams->toArray();

        // 开启事务
        $connection = \Yii::$app->db->beginTransaction();
        // 基础数据入库
        if ($goodsAttrParams->id > 0) {
            $data['utime'] = time();
            $result = $dbObj->updateById($goodsAttrParams->id, $data);
        } else {
            $data['ctime'] = time();
            $result = $dbObj->insert($data);
        }
        if ($result === false) {
            //失败回滚
            $connection->rollback();
            return $this->reJson([$result], '数据入库失败!', 400);
        }

        // 提交事务
        $connection->commit();

        return $this->reJson([$result]);
    }
}
