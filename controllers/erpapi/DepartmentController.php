<?php

    /**
     * 部门
     * @Author: mawei
     * @Date:   2021-12-22
     * @Last Modified by:   mawei
     * @Last Modified time: 2021-12-23
     */
    namespace app\controllers\erpapi;

    use Yii;
    use Closure;
    use app\controllers\InitController;
    use system\common\{ServiceFactory,TableMap};
    use system\traits\BindBeanParamsTrait;
    use system\beans\KpiBeans;


    class DepartmentController extends InitController {

        use BindBeanParamsTrait;

        /**
         * 返回企业下的部门
         * @return [type] [description]
         * @Date   2021-12-22T17:49:44+0800
         * @Author MaWei <1123265518@qq.com>
         * @Link   http://mawei.live
         */
        function actionList() {
            $list = ServiceFactory::getInstance('BaseDB',TableMap::Department)->getListByCondition(['enterprise_id' => $this->enterpriseId,'id,name']);

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
        function actionGroup($_departmentId) {
            $group = ServiceFactory::getInstance('BaseDB',TableMap::DepartmentGroup)->getListByCondition(['department_id' => $_departmentId],'id,name');
        }


    }
