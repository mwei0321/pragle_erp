<?php

/* 积分
 * @Author: MaWei 
 * @Date: 2022-01-23 21:22:31 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-01-23 22:02:48
 */

namespace app\controllers\erpapi;

use app\controllers\InitController;
use system\common\{ServiceFactory, TableMap};
use system\traits\BindBeanParamsTrait;
use system\beans\order\OrderBeans;

class OrderController extends InitController
{
	use BindBeanParamsTrait;

	/**
	 * 
	 */
	function actionGetlist(OrderBeans $orderBeans) {

		$list = ServiceFactory::getInstance("OrderSrv")->getList($orderBeans);
		
        return $this->reJson([
            'items' => $list,
            'count' => $orderBeans->count,
        ]);
	}

	/**
	 * 手动添加订单
	 * @param  OrderBeans $orderBeans
	 * date: 2022-03-09 22:10:56
	 * @author  <mawei.live>
	 * @return void
	 */
	function actionManual(OrderBeans $orderBeans)
	{
		if ($orderBeans->product_id < 1 || $orderBeans->staff_id < 1) {
			return $this->reJson($orderBeans->toArray(), "param error", 400);
		}

		$orderBeans->money_type = strtoupper($orderBeans->money_type);

		// 写入订单
		$result = ServiceFactory::getInstance("OrderSrv")->ManualEntry($orderBeans);
		if ($result < 1) {
			return $this->reJson([$result], "write fail", 400);
		}

		return $this->reJson();
	}

	/**
	 * 删除订单
	 * @param  OrderBeans $orderBeans
	 * date: 2022-03-18 10:40:47
	 * @author  <mawei.live>
	 * @return void
	 */
	function actionDelete(OrderBeans $orderBeans)
	{
		if ($orderBeans->id < 1) {
			return $this->reJson([], 'param error', 400);
		}

		// 实例化对象并调用
		if (ServiceFactory::getInstance("BaseDB", TableMap::Order)->delById($orderBeans->id, ["is_delete" => 1, "updated_at" => time()]) === false) {
			return $this->reJson([], 'delete fail', 400);
		}

		return $this->reJson();
	}
}
