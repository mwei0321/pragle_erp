<?php

/* 积分
 * @Author: MaWei 
 * @Date: 2022-01-23 21:17:21 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-01-23 21:46:34
 */

namespace system\beans\score;

use system\common\BaseBean;

class ScoreBeans extends BaseBean
{
    public $id            = 0;
    public $type          = 1; // 类型 （1.个人 2.团队）
    public $user_id       = 0; // 跟进人
    public $action_id     = 0;
    public $enterprise_id = 0; // 企业ID
    public $department_id = null; // 部门ID
}
