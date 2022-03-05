<?php

/* 动作KPI服务
 * @Author: MaWei 
 * @Date: 2022-01-06 10:35:44 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-01-17 23:06:01
 */

namespace system\services\order;

use yii\db\Query;
use system\common\{TableMap, ServiceFactory};
use system\beans\order\OrderBeans;

class OrderServices
{

    function ManualEntry(OrderBeans $orderBeans) {
        $srvObj = ServiceFactory::getInstance("BaseDB", TableMap::Order);

        $data = [
            "enterprise_id" => $orderBeans->enterprise_id,
            "user_id" => $orderBeans->user_id,
            "belong_to" => $orderBeans->staff_id,
            "order_num" => HelperFuns::getOrderSN(),
            "total_amount" => $orderBeans->total_amount,
            "order_type" => 2,
            "status" => 2,
            "created_at" => time(),
        ];
        $result = $srvObj->insert($data);
        if($result < 1) {
            return -1;
        }

        // 提取商品信息
        $product = ServiceFactory::getInstance("ProdectSrv")->getProductDeatil($orderBeans->product_id);

        // 写入商品信息
        $product = [
            "enterprise_id" => $product["enterprise_id"],
            "order_num" => $data["order_num"],
            "commodity_id" => $orderBeans->product_id,
            "detail_id" => $orderBeans->product_detail_id,
            "number" => $orderBeans->product_number,
            "price" => $orderBeans->product_price,
        ];

        $result = $srvObj->insert($product,TableMap::OrderDetail);
        if($result < 1) {
            return -1;
        }

        return 1;
    }
}