<?php

/* 跟进积分
 * @Author: MaWei 
 * @Date: 2022-01-25 21:09:26 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-02-19 20:34:46
 */

namespace system\services\score;

use yii\db\Query;
use system\common\{TableMap, ServiceFactory};
use system\beans\score\ActionScoreBeans;

class ActionScoreServices
{

    /**
     * 动作跟进添加积分
     * @param  \system\beans\follow\ActionScoreBeans $scoreBeans
     * date: 2022-01-25 21:14:06
     * @author  <mawei.live>
     * @return []
     */
    function addActionFinishScore(ActionScoreBeans $scoreBeans)
    {
        // 初始化
        $dbObj = ServiceFactory::getInstance("BaseDB", TableMap::ActionFollowLog);

        // 提取动作对应积分
        if ($scoreBeans->score > 0) {
            $score = $dbObj->getFieldValById($scoreBeans->action_id, "interior_rank", TableMap::Config);
            if ($score == null) {
                return -1;
            }
        }

        // 给跟进人添加积分
        $scoreId = $dbObj->getFieldValByCondition([
            "enterprise_id" => $scoreBeans->enterprise_id,
            "obj_id"        => $scoreBeans->type == 1 ? $scoreBeans->staff_id : $scoreBeans->department_id,
            "type"          => $scoreBeans->type,
            "year"          => $scoreBeans->year,
            "month"         => $scoreBeans->month,
            "day"           => $scoreBeans->day,
        ], 'id', TableMap::DepartmentAndStaffScore);

        // 判断是写入还是更新
        if (intval($scoreId) > 0) {
            $result = $dbObj->increment("score", "`id` = {$scoreId}", TableMap::DepartmentAndStaffScore, $score, "`utime` = " . time());
        } else {
            $data = [
                "enterprise_id" => $scoreBeans->enterprise_id,
                "obj_id"        => $scoreBeans->type == 1 ? $scoreBeans->staff_id : $scoreBeans->department_id,
                "type"          => $scoreBeans->type,
                "year"          => $scoreBeans->year,
                "month"         => $scoreBeans->month,
                "day"           => $scoreBeans->day,
                "score"         => $score,
                "ctime"         => time(),
            ];
            $result = $dbObj->insert($data, TableMap::DepartmentAndStaffScore);
        }
        if (intval($result) < 1) {
            return -3;
        }

        return 1;
    }
}
