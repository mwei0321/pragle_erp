<?php

/*
 * @Author: MaWei 
 * @Date: 2022-04-09 20:07:07 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-05-09 10:50:38
 */

namespace system\beans\user;

use system\common\BaseBean;

class DepartmentBeans extends BaseBean
{
    public $enterprise_id = 0; // 企业
    public $department_id = 0; // 部门
    public $parent_id = 0; // 父ID
    public $front_state = 1; // 前台是否要展示，0否，1是 
    public $is_tree = 0; // 是否返回树形结构
}
