<?php
/*
 * @Author: MaWei 1123265518@qq.com
 * @Date: 2022-06-29
 * @LastEditors: MaWei 1123265518@qq.com
 * @LastEditTime: 2022-07-10
 * @FilePath: \pragle-erp\controllers\erpapi\ConsultController.php
 * @Description: 咨询
 * 
 * Copyright (c) 2022 by MaWei 1123265518@qq.com, All Rights Reserved. 
 */


namespace app\controllers\erpapi;

use app\controllers\InitController;
use GuzzleHttp\Psr7\Query;
use system\common\{ServiceFactory, TableMap};
use system\traits\BindBeanParamsTrait;
use system\beans\consult\ConsultBeans;
use Hashids\Hashids;

class ConsultController extends InitController
{
    use BindBeanParamsTrait;

    /**
     * 列表
     * @param  ConsultBeans $consultBeans
     * date: 2022-06-29 20:20:14
     * @author  <mawei.live>
     * @return void
     */
    function actionList(ConsultBeans $consultBeans)
    {
        // 提取数据
        $list = ServiceFactory::getInstance("ConsultSrv")->getConsultList($consultBeans);

        return $this->reJson([
            'items' => $list,
            'count' => $consultBeans->count,
        ]);
    }



    /**
     * 数据入库
     * @param  ConsultBeans $consultBeans
     * date: 2022-06-29 20:11:42
     * @author  <mawei.live>
     * @return void
     */
    function actionUpdate(ConsultBeans $consultBeans)
    {

        $hashids = new Hashids("domedea_website", 32);
        $tmp = $hashids->decode($consultBeans->identity);
        $enterpriseId = $tmp[0] ?? 0;
        $data = [
            "enterprise_id" => $enterpriseId,
            "name"          => $consultBeans->name,
            "contact"       => $consultBeans->contact,
            "description"   => $consultBeans->description,
        ];
        // 实例化对象
        $srvObj = ServiceFactory::getInstance("BaseDB", TableMap::Consult);

        if ($consultBeans->id) {
            $data["utime"] = time();
            // 实例化对象并调用
            $result = $srvObj->updateById($consultBeans->id, $data);
        } else {
            $data["ctime"] = time();
            $result = $srvObj->insert($data);
        }
        if ($result === false) {
            return $this->reJson([], "insert fail");
        }


        return $this->reJson([]);
    }
}
