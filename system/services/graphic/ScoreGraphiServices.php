<?php

/* 积分条形图
 * @Author: MaWei 
 * @Date: 2022-01-23 21:52:07 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-01-23 22:09:56
 */


namespace system\services\graphic;

use yii\db\Query;
use system\common\{TableMap, ServiceFactory};
use system\beans\score\ScoreBeans;

class ScoreGraphicServices
{

    /**
     * 积分
     * @param  \system\beans\score\ScoreBeans $scoreParams
     * date: 2022-01-23 22:02:58
     * @author  <mawei.live>
     * @return void
     */
    function getDepartmentScore(ScoreBeans $scoreParams)
    {
        // 字段
        $field = "*";

        // 构建
        $query = (new Query())->from(TableMap::User);

        // 部门
        if ($scoreParams->department_id > 0) {
            // 
        }
    }
}
