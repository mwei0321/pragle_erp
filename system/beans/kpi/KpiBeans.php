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
    public $enterprise_id = 0;
    public $department_id = 0;
    public $staff_id      = 0;
    public $group_id      = 0;
    public $year          = '';
    public $kpi_data      = [];
}
