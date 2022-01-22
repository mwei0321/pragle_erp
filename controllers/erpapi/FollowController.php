<?php

/**
 * kpi
 * @Author: MaWei
 * @Date:   2021-12-22
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-01-17 23:06:00
 */

namespace app\controllers\erpapi;

use app\controllers\InitController;
use system\common\{ServiceFactory, TableMap};
use system\traits\BindBeanParamsTrait;
use system\beans\follow\FollowBeans;

class FollowController extends InitController
{
    use BindBeanParamsTrait;

    /**
     * 动作跟进列表
     * @param  FollowBeans $followParams
     * date: 2022-01-22 23:49:47
     * @author  <mawei.live>
     * @return void
     */
    function actionGetlist(FollowBeans $followParams)
    {
        // 提取数据
        $list = ServiceFactory::getInstance("ActionFollowSrv")->getActionFollowList($followParams);

        return $this->reJson([
            'items' => $list,
            'count' => $followParams->count,
        ]);
    }

    /**
     * 部门员工kpi列表
     * 
     * @param  KpiBeans $kpiParams [description]
     * @return [type] [description]
     * @Date   2021-12-23T17:39:36+0800
     * @Author MaWei <1123265518@qq.com>
     * @Link   http://mawei.live
     */
    function actionUpdate(FollowBeans $followParams)
    {
        // 参数过滤
        if ($followParams->action_id < 1) {
            return $this->reJson([$followParams->department_id], 'param error', 400);
        }

        // DB
        $dbObj = ServiceFactory::getInstance("BaseDB", TableMap::ActionFollow);

        // 提取数据
        $data = $followParams->toArray();
        unset($data['id']);

        // 入库
        if ($followParams->id > 0) {
            $data['utime'] = time();
            unset($date['ctime']);
            $result = $dbObj->updateById($followParams->id, $data);
        } else {
            $data['ctime'] = time();
            $result = $dbObj->insert($data);
        }
        // 提取列表
        if ($result === false) {
            return $this->reJson([$result], "write fail", 400);
        }

        return $this->reJson();
    }
}
