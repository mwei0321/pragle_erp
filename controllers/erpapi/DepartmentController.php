<?php

/**
 * 部门
 * @Author: mawei
 * @Date:   2021-12-22
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-04-09 20:58:31
 */

namespace app\controllers\erpapi;

use app\controllers\InitController;
use system\common\{ServiceFactory, TableMap};
use system\traits\BindBeanParamsTrait;

class DepartmentController extends InitController
{

    use BindBeanParamsTrait;

    /**
     * 返回企业下的部门
     * @return [type] [description]
     * @Date   2021-12-22T17:49:44+0800
     * @Author MaWei <1123265518@qq.com>
     * @Link   http://mawei.live
     */
    function actionList()
    {
        $list = ServiceFactory::getInstance('BaseDB', TableMap::Department)->getListByCondition(['enterprise_id' => $this->enterpriseId, 'id,name']);

        return $this->reJson($list);
    }

    /**
     * 返回部门下的分组
     * @param  [type] $_departmentId [description]
     * @return [type] [description]
     * @Date   2021-12-22T21:13:25+0800
     * @Author MaWei
     * @Link   http://www.mawei.live
     */
    function actionGroup($_departmentId)
    {
        $group = ServiceFactory::getInstance('BaseDB', TableMap::Department)->getListByCondition(['department_id' => $_departmentId], 'id,name');
    }
}
