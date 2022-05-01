<?php

/**
 * 动作统计 
 * @Author: MaWei 
 * @Date: 2022-04-09 20:03:16 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-04-09 21:12:56
 */

namespace app\controllers\erpapi;

use app\controllers\InitController;
use system\common\{ServiceFactory, TableMap};
use system\traits\BindBeanParamsTrait;
use system\beans\config\ConfigBeans;

class ConfigController extends InitController {
    use BindBeanParamsTrait;

    
    function actionIndex() {
        
    }


    function actionGetchild(ConfigBeans $configBeans) {
        // 过滤参数
        if($configBeans->parent_id < 1) {
            return $this->reJson([], '父id不能为空', 400);
        }

        // 提取列表
        $list = ServiceFactory::getInstance("BaseDB",TableMap::Config)->getListByCondition(["parent_id" => $configBeans->parent_id]);

        return $this->reJson($list);
    }
}