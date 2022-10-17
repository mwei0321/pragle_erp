<?php
/*
 * @Author: MaWei 
 * @Date: 2022-10-13 10:46:45 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-10-13 11:27:01
 */

namespace app\controllers\erpapi;

use app\controllers\InitController;
use system\beans\attribute\AttributeBeans;
use system\common\{ServiceFactory, TableMap};
use system\traits\BindBeanParamsTrait;

use yii\db\Query;

class AttributeController extends InitController
{
    use BindBeanParamsTrait;


    /**
     * 插入属性名
     *
     * @param  \system\beans\attribute\AttributeBeans $AttributeParams
     * @date: 2022-10-13 11:00:12
     * @author  <mawei.live>
     * @return void
     */
    function actionUpdate(AttributeBeans $AttributeParams)
    {
        // 初始化数据库
        $dbObj = ServiceFactory::getInstance('BaseDB', TableMap::AttributeName);

        // 转存数据
        $data = $AttributeParams->toArray();


        // 开启事务
        $connection = \Yii::$app->db->beginTransaction();
        // 基础数据入库
        if ($AttributeParams->id > 0) {
            $result = $dbObj->updateById($AttributeParams->id, $data);
        } else {
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
