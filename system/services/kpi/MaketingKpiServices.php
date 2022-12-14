<?php

/**
 * kpi
 * @Author: mawei
 * @Date:   2021-12-23
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-01-06 10:42:19
 */

namespace system\services\kpi;

use yii\db\Query;
use system\common\{TableMap, ServiceFactory};
use system\beans\kpi\KpiBeans;

class MaketingKpiServices
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
        $field = 'department_id,group_id,year,GROUP_CONCAT(`target` ORDER BY `month` ASC) as `target`,GROUP_CONCAT(`completed` ORDER BY `month` ASC) as `completed`';

        // 构建条件
        $query = (new Query())->select($field)
            ->from(TableMap::DepartmentMarketingKpi)
            ->where([
                'enterprise_id' => $kpiParams->enterprise_id,
                'year'          => $kpiParams->year,
            ]);

        // 提取当前部门和下一级部门
        if ($kpiParams->department_id > 0) {
            $groupIds = (new Query())->from(TableMap::Group)
                ->select('id')
                ->where([
                    'type'      => 2,
                    'parent_id' => $kpiParams->department_id,
                ])
                ->all();
            $kpiParams->group_ids = $groupIds ? array_column($groupIds, 'id') : [];
        }

        // 部门
        if ($kpiParams->department_id) {
            $query->andWhere(['department_id' => $kpiParams->department_id]);
        }

        // 组
        if ($kpiParams->group_ids) {
            $query->andWhere(['in', 'group_id', $kpiParams->group_ids]);
        }

        // 查出所
        $group = $query->groupBy('group_id')
            ->all();

        // 分组处理
        if ($group && is_array($group)) {
            foreach ($group as $k => $v) {
                $group[$k]['target'] = explode(',', $v['target']);
                $group[$k]['completed'] = explode(',', $v['completed']);
            }
        } else {
            $group = [];
        }

        // 员工
        $staff = $this->getGroupInStaffMarketingKpi($kpiParams);

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
     * @return array
     */
    function getGroupInStaffMarketingKpi(KpiBeans $kpiParams)
    {
        // 字段
        $field = 'sk.staff_id,sk.year,u.username,u.first_name,u.last_name,u.department,GROUP_CONCAT(`sk`.`target` ORDER BY `month` ASC ) as `target`,GROUP_CONCAT(`sk`.`completed` ORDER BY `month` ASC) as `completed`';

        // 提取数据
        $list = (new Query())->select($field)
            ->from(TableMap::StaffMarketingKpi . ' as sk')
            ->leftJoin(TableMap::Staff . ' as u', 'u.id = sk.staff_id')
            ->leftJoin(TableMap::GroupMember . ' as gm', 'gm.target_id = u.id')
            ->where([
                'sk.year'          => $kpiParams->year,
            ])
            ->andWhere(['in', 'gm.group_id', $kpiParams->department_id])
            ->groupBy('sk.staff_id')
            ->orderBy('sk.month ASC')
            ->all();

        foreach ($list as $k => $v) {
            $list[$k]['target'] = explode(',', $v['target']);
            $list[$k]['completed'] = explode(',', $v['completed']);
        }

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
     * 返回KPI历史年份列表
     *
     * @param  \system\beans\kpi\KpiBeans $kpiParams
     * date: 2022-01-06 10:28:05
     * @author  <mawei.live>
     *
     * @return array
     */
    function getKpiYears(KpiBeans $kpiParams)
    {
        $year = (new Query())->select("year")
            ->from(TableMap::DepartmentMarketingKpi)
            ->groupBy('year')
            ->all();
        return $year ? array_column($year, 'year') : [];
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
        $dbObj = ServiceFactory::getInstance('BaseDB', TableMap::DepartmentMarketingKpi);

        // 查询是否插入过


        // 开启事务
        $connection = \Yii::$app->db->beginTransaction();

        // 更新数据
        if (count($kpiParams->group_kpi) > 0) {
            foreach ($kpiParams->group_kpi as $key => $val) {
                // 查询是否已插入过
                $isExist = $dbObj->getCount([
                    'department_id' => $kpiParams->department_id,
                    'year'          => $kpiParams->year,
                    'group_id'      => $key,
                ]);
                $month = 1;
                // 部门 KPI 入库
                foreach ($val as $v) {
                    $group['enterprise_id'] = $kpiParams->enterprise_id;
                    $group['department_id'] = $kpiParams->department_id;
                    $group['year']          = $kpiParams->year;
                    $group['month']         = $month;
                    $group['target']        = $v;
                    $group['group_id']      = $key;
                    if ($isExist > 0) {
                        $group['utime'] = time();
                        $result = $dbObj->update([
                            'group_id' => $key,
                            'year' => $kpiParams->year,
                            'month' => $month,
                        ], $group);
                    } else {
                        $group['ctime'] = time();
                        $result = $dbObj->insert($group);
                    }
                    // 判断是否成功
                    if ($result === false) {
                        //失败回滚
                        $connection->rollback();
                        return false;
                    }
                    $month++;
                }
            }
        }

        /************* 部门下的员工KPI ****************************************/
        if (count($kpiParams->staff_kpi) > 0) {
            foreach ($kpiParams->staff_kpi as $key => $val) {
                $month = 1;
                // 查询是否已插入过
                $isExist = $dbObj->getCount([
                    'staff_id' => $key,
                    'year'     => $kpiParams->year,
                ], TableMap::StaffMarketingKpi);
                foreach ($val as $v) {
                    $staff['enterprise_id'] = $kpiParams->enterprise_id;
                    $staff['year']          = $kpiParams->year;
                    $staff['month']         = $month;
                    $staff['target']        = $v;
                    $staff['staff_id']      = $key;
                    // 入库
                    if ($isExist > 0) {
                        $staff['utime'] = time();
                        $result = $dbObj->update([
                            'staff_id' => $key,
                            'year'     => $kpiParams->year,
                            'month'    => $month,
                        ], $staff, TableMap::StaffMarketingKpi);
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
                    $month++;
                }
            }
        }

        // 提交事务
        $connection->commit();

        return true;
    }
}
