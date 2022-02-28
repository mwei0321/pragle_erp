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
     * @param  FollowBeans $followParams [description]
     * @return [type] [description]
     * @Date   2021-12-23T17:39:36+0800
     * @Author MaWei <1123265518@qq.com>
     * @Link   http://mawei.live
     */
    function actionUpdate(FollowBeans $followParams)
    {
        // 参数过滤
        if ($followParams->action_id < 1) {
            return $this->reJson([$followParams->toArray()], 'param error', 400);
        }

        // 部门id过滤
        if ($followParams->type == 2 && intval($followParams->department_id) < 1) {
            return $this->reJson([$followParams->toArray()], 'param error', 400);
        }

        // $followParams->enterprise_id = $this->enterpriseId;
        // $followParams->staff_id      = $this->userId;

        // 跟进时间
        $followParams->follow_time = $followParams->follow_time ? strtotime($followParams->follow_time) : time();

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
            $data['ctime']    = time();
            $result           = $dbObj->insert($data);
            $followParams->id = $result;
        }
        // 
        if ($result === false) {
            return $this->reJson([$result], "write fail", 400);
        }

        // 给跟进的加积分
        if ($followParams->type == 1) {
            $scoreBeans                = new \system\beans\score\ActionScoreBeans();
            $scoreBeans->enterprise_id = $followParams->enterprise_id;
            $scoreBeans->staff_id      = $followParams->user_id;
            $scoreBeans->staff_id      = $followParams->department_id;
            $scoreBeans->type          = $followParams->type;
            $scoreBeans->year          = date("Y", $followParams->follow_time);
            $scoreBeans->month         = date("m", $followParams->follow_time);
            $scoreBeans->day           = date("d", $followParams->follow_time);
            // 实例化对象并调用
            $result = ServiceFactory::getInstance("ActionScoreSrv")->followActionScore($scoreBeans);
        }

        return $this->reJson();
    }
}
