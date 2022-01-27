<?php

/* 跟进积分
 * @Author: MaWei 
 * @Date: 2022-01-25 21:09:26 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-01-25 23:00:40
 */

namespace system\services\score;

use yii\db\Query;
use system\common\{TableMap, ServiceFactory};
use system\beans\score\FollowScoreBeans;

class FollowScoreServices
{

    /**
     * 动作跟进添加积分
     * @param  \system\beans\follow\FollowScoreBeans $scoreBeans
     * date: 2022-01-25 21:14:06
     * @author  <mawei.live>
     * @return []
     */
    function addActionFollowScore(FollowScoreBeans $scoreBeans)
    {
        // 初始化
        $dbObj = ServiceFactory::getInstance("BaseDB", TableMap::FollowScoreLog);

        // 提取动作对应积分
        $score = $dbObj->getFieldValById($scoreBeans->action_id, "interior_rank", TableMap::Config);
        if ($score == null) {
            return -1;
        }

        // 跟进日期
        $day = date("Ymd", $scoreBeans->follow_time);

        // 给跟进人添加积分
        $scoreId = $dbObj->getFieldValByCondition([
            "enterprise_id" => $scoreBeans->enterprise_id,
            "obj_id"        => $scoreBeans->type == 1 ? $scoreBeans->staff_id : $scoreBeans->department_id,
            "type"          => $scoreBeans->type,
            "day"           => $day,
        ], 'id', TableMap::DepartmentAndStaffScore);

        // 判断是写入还是更新
        if (intval($scoreId) > 0) {
            $result = $dbObj->increment("score", "`id` = {$scoreId}", TableMap::DepartmentAndStaffScore, $score, "`utime` = " . time());
        } else {
            $data = [
                "enterprise_id" => $scoreBeans->enterprise_id,
                "obj_id"        => $scoreBeans->type == 1 ? $scoreBeans->staff_id : $scoreBeans->department_id,
                "type"          => $scoreBeans->type,
                "date"          => $day,
                "score"         => $score,
                "ctime"         => time(),
            ];
            $result = $dbObj->insert($data, TableMap::DepartmentAndStaffScore);
        }
        if (intval($result) < 1) {
            return -3;
        }

        // 写入动作跟进积分记录
        $data = [
            "enterprise_id" => $scoreBeans->enterprise_id,
            'action_id'     => $scoreBeans->action_id,
            "staff_id"      => $scoreBeans->staff_id,
            "obj_id"        => $scoreBeans->obj_id,
            'type'          => $scoreBeans->type,
            "score"         => $score,
            "ctime"         => time(),
        ];
        // 入库
        $result = $dbObj->insert($data);
        if (intval($result) < 1) {
            return -5;
        }

        return 1;
    }
}
