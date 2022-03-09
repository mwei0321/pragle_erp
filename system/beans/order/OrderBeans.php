<?php

/*
 * 动作定时任务
 * @Author: MaWei 
 * @Date: 2022-03-05 20:38:50 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-03-05
 */

namespace system\beans\order;

use system\common\BaseBean;

class OrderBeans extends BaseBean
{
    public $enterprise_id     = 0; // 企业
    public $user_id           = 0; // 购买用户
    public $staff_id          = 0; // 员工
    public $total_amount      = 0; // 总金额
    public $order_type        = 2; // 订单类型
    public $order_status      = 2; // 订单状态
    public $pay_type          = 0; // 支付方式
    public $product_id        = 0; // 产品ID
    public $product_detail_id = 0; // 产品规格id
    public $product_number    = 0; // 产品数量
    public $product_price     = 0; // 产品价格
    public $order_num         = ""; // 订单号
}
