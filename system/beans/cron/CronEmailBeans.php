<?php

/*
 * 邮件定时任务
 * @Author: MaWei 
 * @Date: 2022-02-13 20:38:50 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-02-13 20:43:45
 */

namespace system\beans\cron;

use system\common\BaseBean;

class CronEmailBeans extends BaseBean
{
    public $cycle   = 1; // 周期
    public $user_id = 0; // 用户id
    public $stime   = 0; // 开始时间
    public $etime   = 0; // 结束时间
}
