<?php

/* kpi 记录参数
 * @Author: MaWei 
 * @Date: 2022-01-17 23:19:26 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-01-17 23:20:34
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
}
