<?php
/*
 * @Author: MaWei 
 * @Date: 2022-10-13 10:52:25 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-10-13 10:56:27
 */

namespace system\beans\attribute;

use system\common\BaseBean;

class AttributeBeans extends BaseBean
{
    public $id = 0;
    public $pid = 0; // 父级id
    public $type = 1; // 属性类型 1.商品
    public $group_id = 0; // 属性组
    public $attr_name = ""; // 属性名称
    public $status = 1; // 属性状态 (-1.删除 0.隐藏 1.显示)
}
