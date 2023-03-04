<?php

/* 项目跟进
 * @Author: MaWei 
 * @Date: 2022-12-28 09:44:54 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-12-28 14:48:39
 */

namespace app\controllers\erpapi;

use app\controllers\InitController;
use system\traits\BindBeanParamsTrait;
use system\beans\project\ProjectBeans;
use system\common\{TableMap, ServiceFactory};
use yii\db\Query;

class ProjectController extends InitController
{
    use BindBeanParamsTrait;

    /**
     * 跟进项目列表
     *
     * @param  ProjectBeans $projectBeans
     * date: 2022-12-28 09:44:41
     * @author  <mawei.live>
     * @return void
     */
    function actionGetlist(ProjectBeans $projectBeans)
    {

        // 提取列表
        $list = ServiceFactory::getInstance("ProjectSrv")->getList($projectBeans);

        return $this->reJson([
            'items' => $list,
            'count' => $projectBeans->count,
        ]);
    }

    /**
     * 返回跟进详情
     *
     * @param  ProjectBeans $projectBeans
     * date: 2022-12-28 10:01:05
     * @author  <mawei.live>
     * @return void
     */
    function actionGetinfo(ProjectBeans $projectBeans)
    {
        if ($projectBeans->id < 1) {
            return $this->reJson($projectBeans->toArray(), "param error", 400);
        }

        // 提取详情
        $info = (new Query())->from(TableMap::Project)->where(["id" => $projectBeans->id])->one();
        $info['staff_id'] && $info['staff_id'] = json_decode($info['staff_id']);

        return $this->reJson($info);
    }

    /**
     * 更新状态 
     *
     * @param  ProjectBeans $projectBeans
     * date: 2022-12-28 15:07:38
     * @author  <mawei.live>
     * @return void
     */
    function actionState(ProjectBeans $projectBeans)
    {
        if ($projectBeans->id < 1 || !in_array($projectBeans->state, [1, 2, 3, 4, 5])) {
            return $this->reJson($projectBeans->toArray(), "param error", 400);
        }

        // 实例化对象并调用
        if (ServiceFactory::getInstance("BaseDB", TableMap::Project)->updateById($projectBeans->id, ["state" => $projectBeans->state, "utime" => time()]) === false) {
            return $this->reJson([], 'delete fail', 400);
        }

        return $this->reJson();
    }

    /**
     * 项目跟进入库
     *
     * @param  ProjectBeans $projectBeans
     * date: 2022-12-28 10:05:53
     * @author  <mawei.live>
     * @return void
     */
    function actionUpdate(ProjectBeans $projectBeans)
    {
        // 初始化数据库
        $dbObj = ServiceFactory::getInstance('BaseDB', TableMap::Project);

        // 参数错误
        if (is_array($projectBeans->staff_id)) {
            return $this->reJson(["staff_id" => $projectBeans->staff_id], '参数错误', 400);
        }

        // 转存数据
        $data = $projectBeans->toArray();
        $data['staff_id'] = json_encode($projectBeans->staff_id);

        // 开启事务
        $connection = \Yii::$app->db->beginTransaction();
        // 基础数据入库
        if ($projectBeans->id > 0) {
            $data['utime'] = time();
            $result = $dbObj->updateById($projectBeans->id, $data);
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

    /**
     * 删除
     *
     * @param  ProjectBeans $projectBeans
     * date: 2022-12-28 10:06:31
     * @author  <mawei.live>
     * @return void
     */
    function actionDelete(ProjectBeans $projectBeans)
    {
        if ($projectBeans->id < 1) {
            return $this->reJson($projectBeans->toArray(), "param error", 400);
        }

        // 实例化对象并调用
        if (ServiceFactory::getInstance("BaseDB", TableMap::Project)->delById($projectBeans->id, ["is_del" => 1, "utime" => time()]) === false) {
            return $this->reJson([], 'delete fail', 400);
        }

        return $this->reJson();
    }
}
