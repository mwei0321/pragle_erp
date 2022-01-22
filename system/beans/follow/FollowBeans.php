<?php

/* 动作KPI服务
 * @Author: MaWei 
 * @Date: 2022-01-06 10:35:44 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-01-17 23:06:01
 */

namespace system\beans\follow;

use system\common\BaseBean;

class FollowBeans extends BaseBean
{
    public $id            = 0;
    public $type          = 1; // 类型 （1.个人 2.团队）
    public $user_id       = 0; // 跟进人
    public $action_id     = 0;
    public $enterprise_id = 0; // 企业ID
    public $department_id = null; // 部门ID
    public $content       = ''; // 跟进内容
    public $utime         = 0; // 更新时间
    public $ctime         = 0; // 创建时间
}
