<?php
/* 商品属性
 * @Author: MaWei 
 * @Date: 2022-08-29 23:32:39 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-08-29 23:55:17
 */

namespace app\controllers\erpapi;

use app\controllers\InitController;
use GuzzleHttp\Psr7\Query;
use system\common\{ServiceFactory, TableMap};
use system\traits\BindBeanParamsTrait;
use system\beans\consult\ConsultBeans;

class GoodsController extends InitController
{


    function actionIndex()
    {
    }


    function actionInfo() {
        
    }

    function actionTree() {
        
    }

    

    function actionUpdate(BaseBean $BaseBeanParams) {
        // 初始化数据库
        $dbObj = ServiceFactory::getInstance('BaseDB',TableMap::GoodsAttribute);
    
        // 转存数据
        $data = $BaseBeanParams->toArray();
    
        // 开启事务
        $connection = \Yii::$app->db->beginTransaction();
        // 基础数据入库
        if($BaseBeanParams->id > 0){
            $data['utime'] = time();
            $result = $dbObj->updateById($BaseBeanParams->id,$data);
        }else{
            $data['ctime'] = time();
            $result = $dbObj->insert($data);
        }
        if($result === false){
            //失败回滚
            $connection->rollback();
            return $this->reJson([$result],'数据入库失败!',400);
        }
    
        // 提交事务
        $connection->commit();
    
        return $this->reJson([$result]);
    }
}
