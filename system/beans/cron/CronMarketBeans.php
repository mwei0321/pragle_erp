<?php

/*
 * 销售统计参数
 * @Author: MaWei 
 * @Date: 2022-02-19 20:38:50 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-03-29 10:01:26
 */

namespace system\beans\cron;

use system\common\BaseBean;

class CronMarketBeans extends BaseBean
{
    public $enterprise_id = 0; // 企业
    public $department_id = 0; // 部门
    public $staff_id      = 0; // 员工
    public $action_id     = 0; // 动作id
    public $cycle         = 1; // 周期
    public $target        = 0; // 需要完成的目标数
    public $finish        = 0; // 已完成的目标数
    public $score         = 0; // 完成后添加积分数
    public $status        = 0; // 状态 (1.完成 0.未完成)
    public $obj_id        = 0; // 跟进id
    public $year          = 0; // 年
    public $month         = 0; // 月
    public $week          = 0; // 月
    public $day           = 0; // 天
}
