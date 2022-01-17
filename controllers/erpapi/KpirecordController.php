<?php

/* kpi操作记录
 * @Author: MaWei 
 * @Date: 2022-01-17 23:16:55 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-01-17 23:45:01
 */

namespace app\controllers\erpapi;

use app\controllers\InitController;
use system\common\{ServiceFactory, TableMap};
use system\traits\BindBeanParamsTrait;
use system\beans\kpi\KpiBeans;

class KpirecordController extends InitController
{
    use BindBeanParamsTrait;

    function actionGetlist(KpiBeans $kpiParams)
    {
        // 提取列表
        $list = ServiceFactory::getInstance("KpiRecordSrv")->getKpiRecordList($kpiParams);

        return $this->reJson($list);
    }
}
