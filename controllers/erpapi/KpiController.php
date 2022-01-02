<?php

/**
 * kpi
 * @Author: MaWei
 * @Date:   2021-12-22
 * @Last Modified by:   MaWei
 * @Last Modified time: 2021-12-25
 */

namespace app\controllers\erpapi;


use Yii;
use app\controllers\InitController;
use GuzzleHttp\Psr7\ServerRequest;
use phpDocumentor\Reflection\DocBlock\Serializer;
use system\common\{ServiceFactory, TableMap};
use system\traits\BindBeanParamsTrait;
use system\beans\kpi\KpiBeans;

class KpiController extends InitController
{
    use BindBeanParamsTrait;

    /**
     * 部门员工kpi列表
     * 
     * @param  KpiBeans $kpiParams [description]
     * @return [type] [description]
     * @Date   2021-12-23T17:39:36+0800
     * @Author MaWei <1123265518@qq.com>
     * @Link   http://mawei.live
     */
    function actionGetmarketingkpi(KpiBeans $kpiParams)
    {
        // 年初始化
        $kpiParams->year          = $kpiParams->year ?: date('Y');
        $kpiParams->enterprise_id = $this->enterpriseId;

        // 参数过滤
        if ($kpiParams->department_id < 1) {
            return $this->reJson([$kpiParams->department_id], 'param error', 400);
        }

        // 提取列表
        $list = ServiceFactory::getInstance('Kpi')->getDepartmentAndStaffMarketingKpi($kpiParams);

        return $this->reJson($list);
    }

    /**
     * 员工动作kpi列表
     * @param  KpiBeans $kpiParams [description]
     * @return [type] [description]
     * @Date   2021-12-23T17:38:57+0800
     * @Author MaWei <1123265518@qq.com>
     * @Link   http://mawei.live
     */
    function actionStaffaction(KpiBeans $kpiParams)
    {
        // 参数处理
        !$kpiParams->year && $kpiParams->year = date('Y');
        intval($kpiParams->staff_id) < 1 && $kpiParams->staff_id = $this->userId;

        // 提取列表
        $list = ServiceFactory::getInstance('Kpi')->getStaffActionKpi($kpiParams);

        return $this->reJson($list);
    }

    /**
     * 返回部门动作列表
     *
     * @param  KpiBeans $kpiParams
     * date: 2021-12-28 22:27:59
     * @author  <mawei.live>
     * @return void
     */
    function actionDepartmentaction(KpiBeans $kpiParams)
    {
        // 年初始化
        $kpiParams->year          = $kpiParams->year ?: date('Y');
        $kpiParams->enterprise_id = $this->enterpriseId;

        // 部门参数过滤
        if ($kpiParams->department_id < 1) {
            return $this->reJson([$kpiParams->department_id], 'param error', 400);
        }

        // 提取数据
        $list = ServiceFactory::getInstance("Kpi")->getDepartmentActionKpi($kpiParams);

        return $this->reJson($list);
    }

    /**
     * 写入员工动作KPI
     *
     * @param  KpiBeans $kpiParams
     * date: 2021-12-28 23:11:33
     * @author  <mawei.live>
     * @return void
     */
    function actionUpstaffaction(KpiBeans $kpiParams)
    {
        // 参数过滤
        if (!$kpiParams->kpi_data) {
            return $this->reJson([], '参数错误!', 400);
        }

        // 更新入库
        if (!ServiceFactory::getInstance('Kpi')->updateStaffActionKpi($kpiParams)) {
            return $this->reJson([$kpiParams->errCode], $kpiParams->errMsg, 400);
        }

        return $this->reJson([]);
    }

    
    function actionYear(KpiBeans $kpiParams) {
        $year = ServiceFactory::getInstance("Kpi")->getKpiYears($kpiParams);

        return $this->reJson($year);
    }

    /**
     * 写入部门动作KPI
     *
     * @param  KpiBeans $kpiParams
     * date: 2021-12-28 23:12:48
     * @author  <mawei.live>
     * @return void
     */
    function actionUpdepartmentaction(KpiBeans $kpiParams)
    {
        // 参数过滤
        if (!$kpiParams->kpi_data) {
            return $this->reJson([], '参数错误!', 400);
        }

        // 更新入库
        if (!ServiceFactory::getInstance('Kpi')->updateDepartmentActionKpi($kpiParams)) {
            return $this->reJson([$kpiParams->errCode], $kpiParams->errMsg, 400);
        }

        return $this->reJson([]);
    }

    /**
     * 部门&员工kpi写入
     * @return [type] [description]
     * @Date   2021-12-22T21:21:34+0800
     * @Author MaWei
     * @Link   http://www.mawei.live
     */
    function actionUpmarketingkpi(KpiBeans $kpiParams)
    {
        // 参数过滤
        if (!$kpiParams->group_kpi && !$kpiParams->staff_kpi) {
            return $this->reJson([], '参数错误!', 400);
        }
        $kpiParams->year = $kpiParams->year ?: date('Y');

        // 企业ID
        $kpiParams->enterprise_id = $this->enterpriseId;

        // 更新入库
        if (!ServiceFactory::getInstance('Kpi')->updateDepartmentAndStaffMarketingKpi($kpiParams)) {
            return $this->reJson([$kpiParams->errCode], $kpiParams->errMsg, 400);
        }

        return $this->reJson([]);
    }
}
