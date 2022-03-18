<?php

/**
 * kpi
 * @Author: MaWei
 * @Date:   2021-12-22
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-01-17 23:06:00
 */

namespace app\controllers\erpapi;

use app\controllers\InitController;
use system\common\{HelperFuns, ServiceFactory, TableMap};
use system\traits\BindBeanParamsTrait;
use system\beans\kpi\KpiBeans;
use system\beans\kpi\ActionBeans;

class KpiController extends InitController
{
    use BindBeanParamsTrait;

    function actionIndex()
    {
        // // 昨天邮件统计 
        // ServiceFactory::getInstance("EmailCronSrv")->getYesterdayEmailStatistics();
        // // 昨天新跟进动作统计
        ServiceFactory::getInstance("FollowCronSrv")->getYesterdayActionFollow();
        // // 昨天旧跟进动作统计
        ServiceFactory::getInstance("FollowCronSrv")->getYesterdayOldFollow();
        // // 昨天客户统计
        // ServiceFactory::getInstance("CustomerCronSrv")->getYesterdayCustomerStatistics();

        $actionBeans = new ActionBeans();
        $actionBeans->cycle = 1;
        // 员工每天完成统计
        ServiceFactory::getInstance("ActionCronSrv")->staffActionFinishCheck($actionBeans);
    }

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

        // 参数过滤
        if ($kpiParams->department_id < 1) {
            return $this->reJson([$kpiParams->department_id], 'param error', 400);
        }

        // 提取列表
        $list = ServiceFactory::getInstance("MaketingKpiSrv")->getDepartmentAndStaffMarketingKpi($kpiParams);

        return $this->reJson($list);
    }

    /**
     * 返回历史中年份列表
     * @param  KpiBeans $kpiParams
     * date: 2022-01-06 10:52:39
     * @author  <mawei.live>
     * @return json
     */
    function actionYear(KpiBeans $kpiParams)
    {
        $year = ServiceFactory::getInstance("BaseDB", TableMap::ActionYear)->getListByCondition([">", "id", 0]);

        return $this->reJson($year);
    }

    function actionAddyear(KpiBeans $kpiParams)
    {
        if (ServiceFactory::getInstance("BaseDB", TableMap::ActionYear)->insert(['year' => $kpiParams->year]) < 1) {
            return $this->reJson([], "fail", 400);
        }

        return $this->reJson();
    }

    function actionDelyear(KpiBeans $kpiParams)
    {
        if (ServiceFactory::getInstance("BaseDB")->delById(['id' => $kpiParams->id], [], TableMap::ActionYear, true) < 1) {
            return $this->reJson([], "fail", 400);
        }

        return $this->reJson();
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

        // 更新入库
        if (!ServiceFactory::getInstance("MaketingKpiSrv")->updateDepartmentAndStaffMarketingKpi($kpiParams)) {
            return $this->reJson([$kpiParams->errCode], $kpiParams->errMsg, 400);
        }

        return $this->reJson([]);
    }

    /**
     * 删除部门下组的KPI
     * date: 2022-01-11 22:43:10
     * @author  <mawei.live>
     * @return void
     */
    function actionDelgroupmarketing(KpiBeans $kpiParams)
    {
        if ($kpiParams->id < 1) {
            return $this->reJson([], '参数错误!', 400);
        }

        // 删除记录
        $result = ServiceFactory::getInstance("BaseDB")->delById($kpiParams->id, [], TableMap::DepartmentMarketingKpi, true);
        if (!$result) {
            return $this->reJson([], '删除失败!', 400);
        }

        return $this->reJson();
    }

    /**
     * 删除员工kpi定义
     * @param  KpiBeans $kpiParams
     * date: 2022-01-11 22:55:22
     * @author  <mawei.live>
     * @return void
     */
    function actionDelstaffmarketing(KpiBeans $kpiParams)
    {
        if ($kpiParams->id < 1) {
            return $this->reJson([], '参数错误!', 400);
        }

        // 删除记录
        $result = ServiceFactory::getInstance("BaseDB")->delById($kpiParams->id, [], TableMap::StaffMarketingKpi, true);
        if (!$result) {
            return $this->reJson([], '删除失败!', 400);
        }

        return $this->reJson();
    }

    //------->>>>>>>------动作KPI------<<<<<<<------>>>>---MaWei@2022-01-06 10:57----<<<<----//

    /**
     * 返回KPI动作列表
     *
     * @param  KpiBeans $kpiBeans
     * date: 2022-01-06 10:24:11
     * @author  <mawei.live>
     *
     * @return json
     */
    function actionGetkpiactionlist(KpiBeans $kpiBeans)
    {
        $list = ServiceFactory::getInstance("ActionKpiSrv")->getKpiActionOptionList($kpiBeans);

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
        // intval($kpiParams->staff_id) < 1 && $kpiParams->staff_id = $this->userId;

        // 提取列表
        $list = ServiceFactory::getInstance("ActionKpiSrv")->getStaffActionKpi($kpiParams);

        return $this->reJson([
            'items' => $list,
            'count' => $kpiParams->count,
        ]);
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
        $kpiParams->year = $kpiParams->year ?: date('Y');

        // 提取数据
        $list = ServiceFactory::getInstance("ActionKpiSrv")->getDepartmentActionKpi($kpiParams);

        return $this->reJson([
            'items' => $list,
            'count' => $kpiParams->count,
        ]);
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
        if (!$kpiParams->staff || !$kpiParams->action) {
            return $this->reJson([], '参数错误!', 400);
        }

        // 更新入库
        if (!ServiceFactory::getInstance("ActionKpiSrv")->updateStaffActionKpi($kpiParams)) {
            return $this->reJson([$kpiParams->errCode], $kpiParams->errMsg, 400);
        }

        return $this->reJson([]);
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
        if (!$kpiParams->action || !$kpiParams->department || !is_array($kpiParams->action) || !$kpiParams->department) {
            return $this->reJson([$kpiParams], '参数错误!', 400);
        }

        // 更新入库
        if (!ServiceFactory::getInstance("ActionKpiSrv")->updateDepartmentActionKpi($kpiParams)) {
            return $this->reJson([$kpiParams->errCode], $kpiParams->errMsg, 400);
        }

        return $this->reJson([]);
    }

    function actionUpaction(KpiBeans $kpiParams)
    {
        if ($kpiParams->id < 1) {
            return $this->reJson([$kpiParams], '参数错误!', 400);
        }
        $kpiParams->enterprise_id = $this->enterpriseId;
    }

    /**
     * 删除员工动作目标kpi
     * @param  KpiBeans $kpiParams
     * date: 2022-01-11 23:14:33
     * @author  <mawei.live>
     * @return void
     */
    function actionDelstaffaction(KpiBeans $kpiParams)
    {
        if ($kpiParams->id < 1) {
            return $this->reJson([], '参数错误!', 400);
        }

        // 删除记录
        $result = ServiceFactory::getInstance("BaseDB", TableMap::StaffActionKpi)->delById($kpiParams->id, ["del_time" => time()]);
        if (!$result) {
            return $this->reJson([], '删除失败!', 400);
        }

        return $this->reJson();
    }

    /**
     * 删除部门动作目标kpi
     * @param  KpiBeans $kpiParams
     * date: 2022-01-11 23:14:59
     * @author  <mawei.live>
     * @return void
     */
    function actionDeldepartmentaction(KpiBeans $kpiParams)
    {
        if ($kpiParams->id < 1) {
            return $this->reJson([], '参数错误!', 400);
        }

        // 删除记录
        $result = ServiceFactory::getInstance("BaseDB", TableMap::DepartmentActionKpi)->delById($kpiParams->id, ["del_time" => time()]);
        if (!$result) {
            return $this->reJson([], '删除失败!', 400);
        }

        return $this->reJson();
    }

    //------->>>>>>>------排行图表------<<<<<<<&&&&>>>>>>---MaWei@2022-01-23 19:17----<<<<<<----//

    /**
     * 销售条图表
     * @param  KpiBeans $kpiParams
     * date: 2022-01-23 20:06:14
     * @author  <mawei.live>
     * @return void
     */
    function actionMarketingbarchat(KpiBeans $kpiParams)
    {
        // 年初始化
        $kpiParams->year          = $kpiParams->year ?: date('Y');

        // 提取列表
        switch ($kpiParams->type) {
                // 个人
            case 1:
                $list = ServiceFactory::getInstance("KpiGraphicSrv")->getStaffMarketingBarChatForMonth($kpiParams);
                break;
                // 部门
            case 2:
                $list = ServiceFactory::getInstance("KpiGraphicSrv")->getDepartmentMarketingBarChatForMonth($kpiParams);
                break;
            default:
                $list = [];
                break;
        }


        return $this->reJson($list);
    }
}
