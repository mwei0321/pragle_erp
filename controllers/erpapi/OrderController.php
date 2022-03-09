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
	 * 手动添加订单
	 * @param  OrderBeans $orderBeans
	 * date: 2022-03-09 22:10:56
	 * @author  <mawei.live>
	 * @return void
	 */
	function actionManual(OrderBeans $orderBeans)
	{
		if ($orderBeans->product_id < 1 || $orderBeans->user_id < 1 || $orderBeans->staff_id < 1) {
			return $this->reJson($orderBeans->toArray(), "param error", 400);
		}

		// 写入订单
		$result = ServiceFactory::getInstance("OrderSrv")->ManualEntry($orderBeans);
		if ($result < 1) {
			return $this->reJson([$result], "write fail", 400);
		}

		return $this->reJson();
	}
}
