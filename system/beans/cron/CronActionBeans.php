<?php

/*
 * 动作定时任务
 * @Author: MaWei 
 * @Date: 2022-02-19 20:38:50 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-02-24 23:26:39
 */

namespace system\beans\cron;

use system\common\BaseBean;

class CronActionBeans extends BaseBean
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
    public $type          = 0; // 类型
    public $stime         = 0; // 开始时间
    public $etime         = 0; // 结束时间
}
