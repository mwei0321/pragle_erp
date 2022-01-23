<?php

/* 积分
 * @Author: MaWei 
 * @Date: 2022-01-23 21:22:31 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-01-23 22:02:48
 */

namespace app\controllers\erpapi;

use app\controllers\InitController;
use system\common\{ServiceFactory, TableMap};
use system\traits\BindBeanParamsTrait;
use system\beans\score\ScoreBeans;

class ScoreController extends InitController
{
    use BindBeanParamsTrait;

    /**
     * 积分条形图
     * @param  ScoreBeans $scoreParams
     * date: 2022-01-23 21:24:33
     * @author  <mawei.live>
     * @return void
     */
    function actionGetbarchat(ScoreBeans $scoreParams)
    {
    }
}
