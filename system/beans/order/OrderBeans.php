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
    public $id                    = 0; // 订单id
    public $enterprise_id         = 0; // 企业id
    public $staff_id              = 0; // 员工id
    public $department_id = 0; // 部门
    public $buyer_enterprise_id  = 0; // 企业
    public $buyer_user_id         = 0; // 购买用户
    public $seller_enterprise_id = 0;
    public $seller_user_id        = 0;
    public $total_amount          = 0; // 总金额
    public $order_type            = 2; // 订单类型
    public $order_status          = 2; // 订单状态
    public $pay_type              = 0; // 支付方式
    public $product_id            = 0; // 产品ID
    public $product_detail_id     = 0; // 产品规格id
    public $product_number        = 0; // 产品数量
    public $product_price         = 0; // 产品价格
    public $order_num             = ""; // 订单号
    public $money_type            = "RMB";
    public $exchange_rate = 0; // 汇率
    public $total_rmb = 0; // 人民币
    public $created_at            = 0; // 创建时间
    public $description           = ""; // 订单说明

}
