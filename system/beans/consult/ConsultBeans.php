<?php
/*
 * @Author: MaWei 1123265518@qq.com
 * @Date: 2022-06-29
 * @LastEditors: MaWei 1123265518@qq.com
 * @LastEditTime: 2022-07-10
 * @FilePath: \pragle-erp\system\beans\consult\ConsultBeans.php
 * @Description: 咨询
 * 
 * Copyright (c) 2022 by MaWei 1123265518@qq.com, All Rights Reserved. 
 */

namespace system\beans\consult;

use system\common\BaseBean;

class ConsultBeans extends BaseBean
{
    public $id = 0;
    public $enterprise_id = 0; // 企业id
    public $keyword = ""; // 关键字
    public $consult_name = ""; // 姓名
    public $consult_nike = ""; // 称谓
    public $company_name = ''; // 公司名称
    public $email = ''; // 邮箱地址
    public $phone = ''; // 电话
    public $is_consult = ''; // 是否咨询
    public $identity = ''; // 
}
