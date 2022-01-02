<?php

/**
 * kpi
 * @Author: mawei
 * @Date:   2021-12-23
 * @Last Modified by:   mawei
 * @Last Modified time: 2021-12-23
 */

namespace system\services\kpi;

use yii\db\Query;
use system\common\{TableMap, ServiceFactory, HelperFuns};
use system\beans\kpi\KpiBeans;

class KpiServices
{

    /**
     * 返回员工的kpi
     * @param  KpiBeans $kpiParams [description]
     * @return [type] [description]
     * @Date   2021-12-23T10:35:24+0800
     * @Author MaWei <1123265518@qq.com>
     * @Link   http://mawei.live
     */
    function getDepartmentAndStaffMarketingKpi(KpiBeans $kpiParams)
    {
        // 字段
        $field = 'department_id,group_id,year,month,target,completed';

        // 构建条件
        $query = (new Query())->select($field)
            ->from(TableMap::DepartmentGroupMarketingKpi)
            ->where([
                'enterprise_id' => $kpiParams->enterprise_id,
                'department_id' => $kpiParams->department_id,
                'year'          => $kpiParams->year,
            ]);

        // 查出所有
        $group = $query->orderBy('month ASC')->all();
        if (!$group) {
            return [];
        }

        // 分组处理
        $group = HelperFuns::classifyMergeArray($group, 'group_id');
        $staff = [];
        foreach ($group as $v) {
            // 提取分组下的员工
            $groupId = $v[0]['group_id'] ?? 0;
            if ($groupId > 0) {
                $kpiParams->group_id = $groupId;
                $staffKpi = $this->getGroupInStaffMarketingKpi($kpiParams);
                ($staffKpi && is_array($staffKpi)) && $staff = array_merge($staff, $staffKpi);
            }
        }

        return [
            'group_kpi' => $group,
            'staff_kpi' => $staff,
        ];
    }

    /**
     * 返回部门分组下的员工KPI
     *
     * @param  \system\beans\kpi\KpiBeans $kpiParams
     * date: 2021-12-28 21:42:42
     * @author  <mawei.live>
     * @return void
     */
    function getGroupInStaffMarketingKpi(KpiBeans $kpiParams)
    {
        // 字段
        $field = 'sk.target,sk.staff_id,sk.month,sk.year,sk.completed,u.username,u.first_name,u.last_name';

        // 提取数据
        $list = (new Query())->select($field)
            ->from(TableMap::StaffMarketingKpi . ' as sk')
            ->leftJoin(TableMap::GroupMember . ' as gm', 'gm.target_id = sk.staff_id')
            ->leftJoin(TableMap::Staff . ' as u', 'u.id = sk.staff_id')
            ->where([
                'sk.year'          => $kpiParams->year,
                'sk.enterprise_id' => $kpiParams->enterprise_id,
                'gm.group_id'      => $kpiParams->group_id,
            ])->orderBy('sk.month ASC')
            ->all();

        return $list;
    }

    /**
     * 返回员工销售KPI列表
     *
     * @param  \system\beans\kpi\KpiBeans $kpiParams
     * date: 2021-12-28 21:50:19
     * @author  <mawei.live>
     * @return array
     */
    function getStaffMarketingKpi(KpiBeans $kpiParams)
    {
        // 字段
        $field = "id,target,month,year,completed";

        // 构建查询
        $query = (new Query())->select($field)
            ->from(TableMap::StaffMarketingKpi)
            ->where([
                "staff_id" => $kpiParams->staff_id
            ]);
        // 年搜索
        if ($kpiParams->year > 0) {
            $query->andWhere(['year' => $kpiParams->year]);
        }

        return $query->orderBy("year DESC,month ASC")->all();
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
     * @return void
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
     * @return void
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

    /**
     * 部门&员工KPI入库更新
     * @return [type] [description]
     * @Date   2021-12-23T11:46:14+0800
     * @Author MaWei <1123265518@qq.com>
     * @Link   http://mawei.live
     */
    function updateDepartmentAndStaffMarketingKpi(KpiBeans $kpiParams)
    {
        // 初始化数据库
        $dbObj = ServiceFactory::getInstance('BaseDB', TableMap::DepartmentGroupMarketingKpi);

        // 查询是否插入过
        $isExist = $dbObj->getCount(['department_id' => $kpiParams->department_id]);

        // 开启事务
        $connection = \Yii::$app->db->beginTransaction();

        // 更新数据
        foreach ($kpiParams->kpi_data as $val) {
            // 部门 KPI 入库
            foreach ($val['group_kpi'] as $v) {
                $group['enterprise_id'] = $kpiParams->enterprise_id;
                $group['department_id'] = $kpiParams->department_id;
                $group['year']          = $kpiParams->year;
                $group['month']         = $v['month'];
                $group['target']        = $v['target'];
                $group['group_id']      = $val['group_id'];
                if (intval($v['id']) > 0) {
                    $group['utime'] = time();
                    $result = $dbObj->updateById($v['id'], $group);
                } elseif ($isExist < 1) {
                    $group['ctime'] = time();
                    $result = $dbObj->insert($group);
                } else {
                    $kpiParams->errCode = 6001;
                    return false;
                }
                // 判断是否成功
                if ($result === false) {
                    //失败回滚
                    $connection->rollback();
                    return false;
                }
            }

            /************* 部门下的员工KPI ****************************************/
            foreach ($val['staff_kpi'] as $v) {
                $staff['enterprise_id'] = $kpiParams->enterprise_id;
                $staff['year']          = $kpiParams->year;
                $staff['month']         = $v['month'];
                $staff['target']        = $v['target'];
                $staff['staff_id']      = $v['staff_id'];
                if (intval($v['id']) > 0) {
                    $staff['utime'] = time();
                    $result = $dbObj->updateById($v['id'], $staff, TableMap::StaffMarketingKpi);
                } else {
                    $staff['ctime'] = time();
                    $result = $dbObj->insert($staff, TableMap::StaffMarketingKpi);
                }

                // 判断是否成功
                if ($result === false) {
                    //失败回滚
                    $connection->rollback();
                    return false;
                }
            }
        }

        // 提交事务
        $connection->commit();

        return true;
    }
}
