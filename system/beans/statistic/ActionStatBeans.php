<?php

/*
 * @Author: MaWei 
 * @Date: 2022-04-09 20:07:07 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-04-09 21:08:56
 */

namespace system\beans\statistic;

use system\common\BaseBean;

class ActionStatBeans extends BaseBean
{
    public $id            = 0;
    public $enterprise_id = 0; // 企业ID
    public $department_id = null; // 部门ID
    public $staff_id      = 0; // 员工ID
    public $year          = ''; // 年
    public $staff_kpi     = [];
    public $department    = [];
    public $action        = null;
    public $stime         = "";
    public $etime         = "";
}
