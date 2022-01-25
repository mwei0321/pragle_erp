<?php

/**
 * kpi
 * @Author: MaWei
 * @Date:   2021-12-22
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-01-25 22:58:42
 */

namespace app\controllers\erpapi;

use app\controllers\InitController;
use system\common\{ServiceFactory, TableMap};
use system\traits\BindBeanParamsTrait;
use system\beans\follow\FollowBeans;
use system\beans\score\FollowScoreBeans;

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

        // 事务

        // 入库
        if ($followParams->id > 0) {
            $data['utime'] = time();
            unset($date['ctime']);
            $result = $dbObj->updateById($followParams->id, $data);
        } else {
            $data['ctime']    = time();
            $result           = $dbObj->insert($data);
            $followParams->id = $result;
        }
        // 提取列表
        if ($result === false) {
            return $this->reJson([$result], "write fail", 400);
        }

        // 积分入库
        $scoreBeans             = new FollowScoreBeans();
        $scoreBeans->enterprise = $this->enterpriseId;
        $scoreBeans->staff_id   = $this->userId;
        $scoreBeans->action_id  = $followParams->action_id;
        $scoreBeans->obj_id     = $followParams->id;
        $scoreBeans->type       = 1;

        // 添加对应积分
        $result = ServiceFactory::getInstance("FollowScoreSrv")->addActionFollowScore($scoreBeans);
        if ($result) {
            return $this->reJson([], "fail", 400);
        }

        return $this->reJson();
    }
}
