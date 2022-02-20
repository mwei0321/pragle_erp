<?php

/* 
 * 跟进积分
 * @Author: MaWei 
 * @Date: 2022-01-25 21:10:36 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-01-25 22:51:23
 */

namespace system\beans\score;

use system\common\BaseBean;

class ActionScoreBeans extends BaseBean
{
    public $id            = 0;
    public $enterprise_id = 0; // 企业ID
    public $department_id = 0; // 部门ID
    public $action_id     = 0; // 动作ID
    public $staff_id      = 0; // 跟进人
    public $score         = 0; // 积分
    public $type          = 1; // 类型 （1.action_follow  2.follow_info)
    public $obj_id        = ''; // 跟进对象表ID
    public $follow_time   = 0; // 跟进时间
    public $year          = 0; // 年
    public $month         = 0; // 月
    public $day           = 0; // 日
}
