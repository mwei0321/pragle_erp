<?php

/**
 * @Author: MaWei
 * @Date:   2021-12-19
 * @Last Modified by:   mawei
 * @Last Modified time: 2021-12-23
 */

namespace system\beans\kpi;

use system\common\BaseBean;


class KpiBeans extends BaseBean
{
    public $id            = 0;
    public $enterprise_id = 0; // 企业ID
    public $department_id = null; // 部门ID
    public $staff_id      = 0; // 员工ID
    public $type          = 1; // 类型 （1.个人 2.团队）
    public $cycle         = 0; // 周期
    public $year          = ''; // 年
    public $name          = ''; // 名称
    public $keyword       = ''; // 关键字
    public $group_kpi     = [];
    public $staff_kpi     = [];
    public $group_ids     = [];
    public $staff         = [];
    public $department    = [];
    public $action        = null;
    public $timeFormat    = "Y-m-d H:i";
    public $is_year_all   = 1; // 是否是一年总和统计
}
