<?php
/*
 * @Author: MaWei 
 * @Date: 2022-01-06 10:35:44 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-01-06 10:42:25
 */

namespace system\services\kpi;

use yii\db\Query;
use system\common\{TableMap, ServiceFactory};
use system\beans\kpi\KpiBeans;

class ActionKpiServices
{
    /**
     * config.group_id (6.对客户营销动作 13.针对员工的动作 15.系统自动统计加分项)
     * 返回KPI动作项列表
     * @param  \system\beans\kpi\KpiBeans $kpiParams
     * date: 2022-01-06 11:01:29
     * @author  <mawei.live>
     * @return array
     */
    function getKpiActionOptionList(KpiBeans $kpiParams)
    {
        // 字段
        $field = 'id,project_name title,index,group_id,parent_id,state,rank,interior_rank';

        // 提取记录
        $query = (new Query())->select($field)
            ->from(TableMap::Config)
            ->where([
                'and',
                ['in', 'group_id', [6, 15]],
                ['>', 'parent_id', '0']
            ]);

        if ($kpiParams->type == 1) {
            $query->orWhere([
                'and',
                ['group_id' => 13],
                ['in', 'state', [1, 3]]
            ]);
        } elseif ($kpiParams->type == 2) {
            $query->orWhere([
                'and',
                ['group_id' => 13],
                ['in', 'state', [2, 3]]
            ]);
        } else {
            return [];
        }

        $list = $query->orderBy('group_id ASC,index ASC')
            ->all();

        return $list;
    }

    /**
     * 返回员工动作KPI列表
     * @param  KpiBeans $kpiParams 条件
     * @return [type] [description]
     * @Date   2021-12-23T11:08:53+0800
     * @Author MaWei <1123265518@qq.com>
     * @Link   http://mawei.live
     */
    function getStaffActionKpi(KpiBeans $kpiParams)
    {
        // 字段
        $field = 'sa.id,sa.cycle,sa.action_id,sa.action_value,sa.action_type,sa.staff_id,sa.year';

        // 构建条件
        $query = (new Query())->select($field)
            ->from(TableMap::StaffActionKpi . ' as sa')
            ->leftJoin(TableMap::Config . ' as c', 'c.id = sa.action_id')
            ->where([
                'sa.staff_id' => $kpiParams->staff_id,
            ]);

        // 年搜索
        if ($kpiParams->year > 0) {
            $query->where(['year' => $kpiParams->year]);
        }

        return $query->all();
    }

    /**
     * 获取部门动作kpi
     *
     * @param  \system\beans\kpi\KpiBeans $kpiParams
     * date: 2021-12-28 22:33:15
     * @author  <mawei.live>
     * @return array
     */
    function getDepartmentAction(KpiBeans $kpiParams)
    {
        // 字段
        $field = 'sa.action_id,sa.action_value,sa.action_type,sa.staff_id,sa.year';

        // 构建条件
        $query = (new Query())->select($field)
            ->from(TableMap::DepartmentActionKpi . ' as sa')
            ->leftJoin(TableMap::Config . ' as c', 'c.id = sa.action_id')
            ->where([
                'sa.staff_id' => $kpiParams->staff_id,
            ]);

        // 年搜索
        if ($kpiParams->year > 0) {
            $query->where(['year' => $kpiParams->year]);
        }

        return $query->orderBy("sa.month ASC")->all();
    }


    /**
     * 员工动作KPI
     * @param  KpiBeans $kpiParams [description]
     * @return [type] [description]
     * @Date   2021-12-23T15:13:18+0800
     * @Author MaWei <1123265518@qq.com>
     * @Link   http://mawei.live
     */
    function updateStaffActionKpi(KpiBeans $kpiParams)
    {
        // 初始化数据库
        $dbObj = ServiceFactory::getInstance('BaseDB', TableMap::StaffActionKpi);

        // 查询是否插入过
        $isExist = $dbObj->getCount(['staff_id' => $kpiParams->staff_id]);

        // 开启事务
        $connection = \Yii::$app->db->beginTransaction();

        // 数据入库
        foreach ($kpiParams->kpi_data as $v) {
            $action['enterprise_id'] = $kpiParams->enterprise_id;
            $action['action_id']     = $v['action_id'];
            $action['action_value']  = $v['action_value'];
            $action['action_type']   = $v['action_type'];
            // 入库
            if (intval($v['id']) > 0) {
                $action['utime'] = time();
                $result = $dbObj->updateById($v['id'], $action);
            } else {
                $action['ctime'] = time();
                $result = $dbObj->insert($action);
            }
        }

        // 判断是否成功
        if ($result === false) {
            //失败回滚
            $connection->rollback();
            return false;
        }

        // 提交事务
        $connection->commit();

        return true;
    }

    /**
     * 部门动作KPI入库
     *
     * @param  \system\beans\kpi\KpiBeans $kpiParams
     * date: 2021-12-28 23:13:59
     * @author  <mawei.live>
     * @return boolean
     */
    function updateDepartmentActionKpi(KpiBeans $kpiParams)
    {
        // 初始化数据库
        $dbObj = ServiceFactory::getInstance('BaseDB', TableMap::StaffActionKpi);

        // 查询是否插入过
        $isExist = $dbObj->getCount(['staff_id' => $kpiParams->staff_id]);

        // 开启事务
        $connection = \Yii::$app->db->beginTransaction();

        // 数据入库
        foreach ($kpiParams->kpi_data as $v) {
            $action['enterprise_id'] = $kpiParams->enterprise_id;
            $action['action_id']     = $v['action_id'];
            $action['action_value']  = $v['action_value'];
            $action['action_type']   = $v['action_type'];
            // 入库
            if (intval($v['id']) > 0) {
                $action['utime'] = time();
                $result = $dbObj->updateById($v['id'], $action);
            } else {
                $action['ctime'] = time();
                $result = $dbObj->insert($action);
            }
        }

        // 判断是否成功
        if ($result === false) {
            //失败回滚
            $connection->rollback();
            return false;
        }

        // 提交事务
        $connection->commit();

        return true;
    }
}
