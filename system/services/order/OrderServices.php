<?php

/* 动作KPI服务
 * @Author: MaWei 
 * @Date: 2022-01-06 10:35:44 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-04-06 11:54:39
 */

namespace system\services\order;

use yii\db\Query;
use system\common\{TableMap, ServiceFactory, HelperFuns};
use system\beans\order\OrderBeans;
use yii\db\Expression;

class OrderServices
{

    function getList(OrderBeans $orderBeans) {
        $field = '`o`.`id` AS `order_id`,`o`.`enterprise_id` as `buyer_enterprise_id`,`o`.`user_id` as `buyer_user_id`,`target_id`,`parent_id`,`o`.`order_num`,`order_type`,`post_num`,';
        $field .= '`receiver_mobile`,`receiver_name`,`receiver_address`,`real_amount`,`total_amount`,`post_amount`,`o`.`fee`,`o`.`commission`,`o`.`currency`,`o`.`exchange_rate`,`o`.`total_rmb`,';
        $field .= '`o`.`pay_type`,`o`.`pay_at`,`o`.`status`,`description`,`o`.`money_type`,`o`.`created_at`,`o`.`updated_at`,od.user_id as seller_user_id,od.enterprise_id as seller_enterprise_id';

        // 构建查询
        $query = (new Query())->from(TableMap::Order." AS o")
            ->leftJoin(TableMap::OrderDetail." AS od","od.order_num = o.order_num")
            ->where([
                "is_delete" => 0,
            ]);

        // 部门搜索
        if ($orderBeans->department_id > 0) {
            $staffIds = ServiceFactory::getInstance("DepartmentSrv")->getDepartmentStaffById($orderBeans->department_id);
            if($staffIds) {
                $query->andWhere(["in",'od.user_id',$staffIds]);
            }else {
                return [];
            }
        }
        
        // 订单号搜索
        if ($orderBeans->order_num) {
            $query->andWhere(['order_num' => $orderBeans->order_num]);
        }

        // 用户搜索
        if($orderBeans->staff_id > 0) {
            $query->andWhere(['od.user_id' => $orderBeans->staff_id]);
        }

        $count = $query->select("od.id")->count();
        // 分页
        $orderBeans->page($count);

        // 排序提取
        $list = $query->select($field)
            ->limit($orderBeans->limit)
            ->offset($orderBeans->offset)
            ->orderBy("o.id DESC")
            ->all();

        // 企业
        foreach($list as $k => $v) {
            $list[$k]['enterprise'] = ServiceFactory::getInstance("BaseDB",TableMap::Enterprise)->getInfoById($v["buyer_enterprise_id"],"id,fullname,shortname");
        }

        return $list;
    }



    /**
     * 返回员工订单销售统计
     * @param  array $_staffIds
     * @param  string $_year
     * date: 2022-04-03 22:12:42
     * @author  <mawei.live>
     * @return array
     */
    function getOrderMarketByStaffIds($_staffIds, $_year)
    {
        $stime = strtotime($_year . "-01-01 00:00:00");
        $etime = strtotime($_year . "-12-30 24:00:00");

        // 字段
        $feild = "CONCAT_WS('-',od.user_id , FROM_UNIXTIME(`created_at`,'%d')) AS user,SUM(`total_amount`) cnt";

        // 构造查询
        return (new Query())->from(TableMap::Order . ' AS o')
            ->select(new Expression($feild))
            ->leftJoin(TableMap::OrderDetail . ' AS od', 'od.order_num = o.order_num')
            ->where([
                'and',
                ['>', 'o.created_at', $stime],
                ['<', 'o.created_at', $etime],
                ["in", 'od.user_id', $_staffIds]
            ])
            ->groupBy("od.user_id,month")
            ->all();
    }

    /**
     * 返回部门订单销售统计 
     * @param  array $_departmentIds
     * @param  string $_year
     * date: 2022-04-03 22:21:25
     * @author  <mawei.live>
     * @return array
     */
    function getOrderMarketByDepartment($_departmentIds, $_year, $_departmentId)
    {
        $stime = strtotime($_year . "-01-01 00:00:00");
        $etime = strtotime($_year . "-12-30 24:00:00");

        // 字段
        $feild = "CONCAT_WS('-',{$_departmentId} , FROM_UNIXTIME(o.`created_at`,'%d')) AS department,SUM(`total_amount`) cnt";

        // 构造查询
        return (new Query())->from(TableMap::Order . ' AS o')
            ->select(new Expression($feild))
            ->leftJoin(TableMap::OrderDetail . ' AS od', 'od.order_num = o.order_num')
            ->leftJoin(TableMap::User . ' AS u', 'u.id = od.user_id')
            ->where([
                'and',
                ['>', 'o.created_at', $stime],
                ['<', 'o.created_at', $etime],
                ["in", 'u.department', $_departmentIds]
            ])
            ->groupBy("month")
            ->all();
    }

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
            "enterprise_id" => $orderBeans->buyer_enterprise_id,
            "user_id"       => $orderBeans->buyer_user_id,
            "target_id" => $orderBeans->buyer_user_id,
            "order_num"     => $orderBeans->order_num ?: HelperFuns::getOrderSN(),
            "total_amount"  => $orderBeans->total_amount,
            "order_type"    => 2,
            "status"        => 2,
            "description"   => $orderBeans->description,
            "money_type"    => $orderBeans->money_type,
            "exchange_rate" => $orderBeans->exchange_rate,
            "total_rmb"     => $orderBeans->total_rmb,
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
            "enterprise_id" => $orderBeans->seller_enterprise_id,
            "user_id"       => $orderBeans->seller_user_id,
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
