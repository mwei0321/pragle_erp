<?php
/*
 * 产品视频
 * @Author: MaWei 
 * @Date: 2023-01-30 10:44:44 
 * @Last Modified by: MaWei
 * @Last Modified time: 2023-01-30 15:14:25
 */

namespace app\controllers\erpapi;

use app\controllers\InitController;
use system\traits\BindBeanParamsTrait;
use system\beans\goods\VideoBeans;
use system\common\{TableMap, ServiceFactory};
use yii\db\Query;

class VideoController extends InitController
{
    use BindBeanParamsTrait;

    /**
     * 产品视频列表
     *
     * @param  VideoBeans $videoBeans
     * date: 2022-12-28 09:44:41
     * @author  <mawei.live>
     * @return void
     */
    function actionGetlist(VideoBeans $videoBeans)
    {

        // 提取列表
        $list = ServiceFactory::getInstance("VideoSrv")->getList($videoBeans);

        return $this->reJson([
            'items' => $list,
            'count' => $videoBeans->count,
        ]);
    }

    /**
     * 返回视频详情
     *
     * @param  VideoBeans $videoBeans
     * date: 2022-12-28 10:01:05
     * @author  <mawei.live>
     * @return void
     */
    function actionGetinfo(VideoBeans $videoBeans)
    {
        if ($videoBeans->id < 1) {
            return $this->reJson($videoBeans->toArray(), "param error", 400);
        }

        // 提取详情
        $info = (new Query())->from(TableMap::Video)->where(["id" => $videoBeans->id])->one();

        return $this->reJson($info);
    }

    /**
     * 更新状态 
     *
     * @param  VideoBeans $videoBeans
     * date: 2022-12-28 15:07:38
     * @author  <mawei.live>
     * @return void
     */
    function actionStatus(VideoBeans $videoBeans)
    {
        if ($videoBeans->id < 1 || !in_array($videoBeans->status, [1, 0])) {
            return $this->reJson($videoBeans->toArray(), "param error", 400);
        }

        // 实例化对象并调用
        if (ServiceFactory::getInstance("BaseDB", TableMap::Video)->updateById($videoBeans->id, ["status" => $videoBeans->status, "utime" => time()]) === false) {
            return $this->reJson([], 'delete fail', 400);
        }

        return $this->reJson();
    }

    /**
     * 项目视频入库
     *
     * @param  VideoBeans $videoBeans
     * date: 2022-12-28 10:05:53
     * @author  <mawei.live>
     * @return void
     */
    function actionUpdate(VideoBeans $videoBeans)
    {
        // 初始化数据库
        $dbObj = ServiceFactory::getInstance('BaseDB', TableMap::Video);

        // 转存数据
        $data = $videoBeans->toArray();

        // 开启事务
        $connection = \Yii::$app->db->beginTransaction();
        // 基础数据入库
        if ($videoBeans->id > 0) {
            $data['utime'] = time();
            $result = $dbObj->updateById($videoBeans->id, $data);
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
     * @param  VideoBeans $videoBeans
     * date: 2022-12-28 10:06:31
     * @author  <mawei.live>
     * @return void
     */
    function actionDelete(VideoBeans $videoBeans)
    {
        if ($videoBeans->id < 1) {
            return $this->reJson($videoBeans->toArray(), "param error", 400);
        }

        // 实例化对象并调用
        if (ServiceFactory::getInstance("BaseDB", TableMap::Video)->delById($videoBeans->id, ["is_del" => 1, "utime" => time()]) === false) {
            return $this->reJson([], 'delete fail', 400);
        }

        return $this->reJson();
    }
}
