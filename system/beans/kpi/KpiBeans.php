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
    public $enterprise_id = 0;
    public $department_id = 0;
    public $staff_id      = 0;
    public $year          = '';
    public $type          = 1;
    public $group_kpi     = [];
    public $staff_kpi     = [];
    public $group_ids     = [];
}
