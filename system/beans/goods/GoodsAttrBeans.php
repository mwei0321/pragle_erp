<?php
/*
* @Author: MaWei 1123265518@qq.com
* @Date: 2022-07-04
* @LastEditors: MaWei 1123265518@qq.com
* @LastEditTime: 2022-07-04
* @FilePath: \Pragle_erp\system\beans\goods\GoodsAttrBeans.php
* @Description:
*
* Copyright (c) 2022 by MaWei 1123265518@qq.com, All Rights Reserved.
*/

namespace system\beans\goods;

use system\common\BaseBean;

class GoodsAttrBeans extends BaseBean
{
    public $id = 0;
    public $type = 0; // 类型 （1.个人 2.团队）
    public $staff_id = 0; // 跟进员工ID
    public $action_id = 0;
    public $enterprise_id = 0; // 企业ID
    public $department_id = 0; // 部门ID
    public $content = ''; // 跟进内容
    public $follow_time = 0; // 跟进时间
    public $attachment_url = ''; // 附件
}
