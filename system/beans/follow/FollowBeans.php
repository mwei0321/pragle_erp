<?php

/* 动作跟进
 * @Author: MaWei 
 * @Date: 2022-01-06 10:35:44 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-01-23 21:18:21
 */

namespace system\beans\follow;

use system\common\BaseBean;

class FollowBeans extends BaseBean
{
    public $id            = 0;
    public $type          = 1; // 类型 （1.个人 2.团队）
    public $staff_id      = 0; // 跟进员工ID
    public $action_id     = 0;
    public $enterprise_id = 0; // 企业ID
    public $department_id = 0; // 部门ID
    public $content       = ''; // 跟进内容
    public $follow_time   = 0; // 跟进时间
}
