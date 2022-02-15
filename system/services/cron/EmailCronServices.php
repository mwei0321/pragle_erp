<?php

/*
 * 邮件相关定时任务
 * @Author: MaWei 
 * @Date: 2022-02-13 20:20:18 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-02-13 20:56:14
 */

namespace system\services\graphic;

use yii\db\Query;
use system\common\{HelperFuns, TableMap, ServiceFactory};
use system\beans\cron\CronEmailBeans;

class EmailCronServices
{

    function actionEmail(CronEmailBeans $_cronEmailBeans)
    {
        switch ($_cronEmailBeans->cycle) {
            case '1':
                $time = date("Y-m-d", strtotime("-1 day"));
                $_cronEmailBeans->stime = strtotime($time);
                $_cronEmailBeans->etime = strtotime($time . " 23:59:59");
                break;
            case '2':
                $_cronEmailBeans->stime = strtotime(date("Y-m-d", strtotime("-1 week")));
                break;
            case '3':
                $_cronEmailBeans->stime = strtotime(date("Y-m-d", strtotime("-1 day")));
                break;
            case '4':
                $_cronEmailBeans->stime = strtotime(date("Y-m-d", strtotime("-1 day")));
                break;

            default:
                # code...
                break;
        }
    }

    /**
     * 返回邮件的统计
     * @param  \system\beans\cron\CronEmailBeans $_cronEmailBeans
     * date: 2022-02-15 18:33:20
     * @author  <mawei.live>
     * @return array
     */
    function getEmailStatistics(CronEmailBeans $_cronEmailBeans)
    {
        // 时间过滤
        if ($_cronEmailBeans->stime < 1 || $_cronEmailBeans->etime < 1) {
            return [];
        }

        // 构建查询
        $query = (new Query())->from(TableMap::TaskQueue . " AS tq")
            ->leftJoin(TableMap::TaskDistribute . " AS td", "td.task_id = tq.id")
            ->where([
                "and",
                ["in", "tq.type", [1, 2, 3]],
                ["td.state" => 0],
                [">=", "td.created_at", $_cronEmailBeans->stime],
                ["<=", "td.created_at", $_cronEmailBeans->etime],
            ]);

        // 用户
        if ($_cronEmailBeans->user_id) {
            $query->andWhere(["tq.user_id" => $_cronEmailBeans->user_id]);
        }

        return $query->select("tq.user_id,COUNT(*) num")
            ->groupBy("tq.user_id")
            ->all();
    }
}
