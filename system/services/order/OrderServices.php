<?php

/* 动作KPI服务
 * @Author: MaWei 
 * @Date: 2022-01-06 10:35:44 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-01-17 23:06:01
 */

namespace system\services\order;

use yii\db\Query;
use system\common\{TableMap, ServiceFactory, HelperFuns};
use system\beans\order\OrderBeans;

class OrderServices
{

    /**
     * 手动录入订单
     * @param  \system\beans\order\OrderBeans $orderBeans
     * date: 2022-03-09 22:12:58
     * @author  <mawei.live>
     * @return void
     */
    function ManualEntry(OrderBeans $orderBeans)
    {
        // 实例化对象
        $srvObj = ServiceFactory::getInstance("BaseDB", TableMap::Order);

        // 开启事务
        $connection = \Yii::$app->db->beginTransaction();
        // 入库数据
        $data = [
            "enterprise_id" => $orderBeans->enterprise_id,
            "user_id"       => $orderBeans->user_id,
            "order_num"     => $orderBeans->order_num ?: HelperFuns::getOrderSN(),
            "total_amount"  => $orderBeans->total_amount,
            "order_type"    => 2,
            "status"        => 2,
            "description"   => $orderBeans->description,
            "money_type"    => $orderBeans->description,
            "created_at"    => time(),
        ];
        $result = $srvObj->insert($data);
        if ($result < 1) {
            $connection->rollBack();
            return -1;
        }

        // 提取商品信息
        // $product = $srvObj->getProductDeatil($orderBeans->product_id);

        // 写入商品信息
        $product = [
            "enterprise_id" => 91796,
            "user_id"       => $orderBeans->staff_id,
            "order_num"     => $data["order_num"],
            "commodity_id"  => $orderBeans->product_id,
            "detail_id"     => $orderBeans->product_detail_id,
            "number"        => $orderBeans->product_number,
            "price"         => $orderBeans->product_price,
        ];

        $result = $srvObj->insert($product, TableMap::OrderDetail);
        if ($result < 1) {
            $connection->rollBack();
            return -1;
        }

        $connection->commit();

        return 1;
    }
}
