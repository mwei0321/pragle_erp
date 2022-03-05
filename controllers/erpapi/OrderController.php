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

class FollowController extends InitController
{

	use BindBeanParamsTrait;


	function actionManual(OrderBeans $orderBeans) {
		if($orderBeans->product_id < 1 || $orderBeans->user_id < 1 || $orderBeans->staff_id < 1) {
			return $this->reJson($orderBeans->toArray(), "param error", 400);
		}

		$result = ServiceFactory::getInstance("OrderSrv")->ManualEntry($orderBeans);

		if($result < 1) {
			return $this->reJson([$result], "write fail", 400);
		}

		return $this->reJson();
	}
}