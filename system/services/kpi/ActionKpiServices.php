<?php

/* 动作KPI服务
 * @Author: MaWei 
 * @Date: 2022-01-06 10:35:44 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-01-17 23:06:01
 */

namespace system\services\kpi;

use yii\db\Query;
use system\common\{TableMap, ServiceFactory};
use system\beans\kpi\KpiBeans;

class ActionKpiServices
{
    /**
     * config.group_id (6.对客户营销动作 13.针对员工的动作 15.系统自动统计加分项)
     *  1为个人，2为团队，3为个人和团队
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
                ['in', 'group_id', [6, 13, 15]],
                ['>', 'parent_id', '0']
            ]);

        // 个人
        // if ($kpiParams->type == 1) {
        //     $query->orWhere([
        //         'and',
        //         ['group_id' => 13],
        //         ['in', 'state', [1, 3]]
        //     ]);
        // } elseif ($kpiParams->type == 2) { // 团队
        //     $query->orWhere([
        //         'and',
        //         ['group_id' => 13],
        //         ['in', 'state', [2, 3]]
        //     ]);
        // } else {
        //     return [];
        // }

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
        $field = 'sa.id,sa.name,sa.cycle,sa.action_id,sa.action_type,sa.action_value,sa.action_value,sa.action_type,sa.staff_id,sa.year,sa.ctime,sa.utime';

        // 构建条件
        $query = (new Query())->select($field)
            ->from(TableMap::StaffActionKpi . ' as sa')
            ->where([
                "enterprise_id" => $kpiParams->enterprise_id,
                "del_time" => 0,
            ]);

        // 关键字
        if ($kpiParams->keyword) {
            $query->andWhere(['like', 'name', $kpiParams->keyword]);
        }

        // 周期
        if ($kpiParams->cycle) {
            $query->andWhere(['cycle' => $kpiParams->cycle]);
        }

        // 动作
        if ($kpiParams->action) {
            $kpiParams->action = explode(',', $kpiParams->action);
            $query->andWhere(['in', 'action_id', $kpiParams->action]);
        }

        // 员工
        if ($kpiParams->staff_id) {
            $query->andWhere(['staff_id' => $kpiParams->staff_id]);
        }

        // 年搜索
        if ($kpiParams->year > 0) {
            $query->andWhere(['year' => $kpiParams->year]);
        }

        // 总条数
        $count = $query->count();
        if ($count < 1) {
            return [];
        }

        // 分页
        $kpiParams->page($count);

        // 排序提取
        $list = $query->leftJoin(TableMap::Config . ' as c', 'c.id = sa.action_id')
            ->limit($kpiParams->limit)
            ->offset($kpiParams->offset)
            ->orderBy("sa.staff_id DESC")
            ->all();

        // 是否格式化时间
        if ($kpiParams->timeFormat) {
            foreach ($list as $k => $v) {
                $list[$k]["utime"] = $v["utime"] ? date($kpiParams->timeFormat, $v["utime"]) : "";
                $list[$k]["ctime"] = date($kpiParams->timeFormat, $v["ctime"]);
            }
        }

        return $list;
    }

    /**
     * 获取部门动作kpi
     *
     * @param  \system\beans\kpi\KpiBeans $kpiParams
     * date: 2021-12-28 22:33:15
     * @author  <mawei.live>
     * @return array
     */
    function getDepartmentActionKpi(KpiBeans $kpiParams)
    {
        // 字段
        $field = 'sa.id,sa.action_id,sa.action_type,sa.action_value,sa.name,sa.cycle,sa.department_id,sa.year,sa.utime,sa.ctime';

        // 构建条件
        $query = (new Query())->select($field)
            ->from(TableMap::DepartmentActionKpi . ' as sa')
            ->where([
                "enterprise_id" => $kpiParams->enterprise_id,
                "del_time" => 0
            ]);

        // 关键字
        if ($kpiParams->keyword) {
            $query->andWhere(['like', 'name', $kpiParams->keyword]);
        }

        // 周期
        if ($kpiParams->cycle) {
            $query->andWhere(['cycle' => $kpiParams->cycle]);
        }

        // 动作
        if ($kpiParams->action) {
            $kpiParams->action = explode(',', $kpiParams->action);
            $query->andWhere(['in', 'action_id', $kpiParams->action]);
        }

        // 部门
        if ($kpiParams->department || $kpiParams->department_id > 0) {
            $kpiParams->department_id > 0 && $kpiParams->department[] = $kpiParams->department_id;
            $query->andWhere(['in', 'department_id', $kpiParams->department]);
        }

        // 年搜索
        if ($kpiParams->year > 0) {
            $query->andWhere(['year' => $kpiParams->year]);
        }

        // 总条数
        $count = $query->count();
        if ($count < 1) {
            return [];
        }

        // 分页
        $kpiParams->page($count);

        // 排序提取
        $list = $query->leftJoin(TableMap::Config . ' as c', 'c.id = sa.action_id')
            ->limit($kpiParams->limit)
            ->offset($kpiParams->offset)
            ->orderBy("sa.department_id DESC")
            ->all();

        // 是否格式化时间
        if ($kpiParams->timeFormat) {
            foreach ($list as $k => $v) {
                $list[$k]["utime"] = $v["utime"] ? date($kpiParams->timeFormat, $v["utime"]) : "";
                $list[$k]["ctime"] = date($kpiParams->timeFormat, $v["ctime"]);
            }
        }

        return $list;
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
        // 判断参数
        if (!$kpiParams->action || !$kpiParams->staff) {
            return 0;
        }

        // 实例化对象
        $srvObj = ServiceFactory::getInstance("BaseDB", TableMap::StaffActionKpi);

        // 开启事务
        $connection = \Yii::$app->db->beginTransaction();

        // 数据入库
        foreach ($kpiParams->staff as $val) {
            $data                  = [];
            $data['enterprise_id'] = $kpiParams->enterprise_id;
            $data['year']          = $kpiParams->year;
            $data['name']          = $kpiParams->name;
            $data['cycle']         = $kpiParams->cycle;
            $data['staff_id']      = $val;
            $data["del_time"] = 0;
            foreach ($kpiParams->action as $v) {
                $data['action_id']    = $v['id'];
                $data["action_value"] = $v["value"];
                // 查询是否有记录
                if ($id = $srvObj->getFieldValByCondition(['staff_id' => $val, "action_id" => $v['id'], "year" => $kpiParams->year], 'id')) {
                    $data["utime"] = time();
                    $result        = $srvObj->updateById($id, $data);
                } else {
                    $data["ctime"] = time();
                    $result        = $srvObj->insert($data);
                }
                // 判断是否成功
                if ($result === false) {
                    //失败回滚
                    $connection->rollback();
                    return 0;
                }
            }
        }

        // 提交事务
        $connection->commit();

        return 1;
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
        // 判断参数
        if (!$kpiParams->action || !$kpiParams->department) {
            return -1;
        }

        // 实例化对象
        $srvObj = ServiceFactory::getInstance("BaseDB", TableMap::DepartmentActionKpi);

        // 开启事务
        $connection = \Yii::$app->db->beginTransaction();

        // 数据入库
        foreach ($kpiParams->department as $val) {
            $data                  = [];
            $data['enterprise_id'] = $kpiParams->enterprise_id;
            $data['year']          = $kpiParams->year;
            $data['name']          = $kpiParams->name;
            $data['cycle']         = $kpiParams->cycle;
            $data['department_id']      = $val;
            $data["del_time"] = 0;
            foreach ($kpiParams->action as $v) {
                $data['action_id']    = $v;
                // 查询是否有记录
                if ($id = $srvObj->getFieldValByCondition(['department_id' => $val, 'action_id' => $v, "year" => $kpiParams->year], 'id')) {
                    $data["utime"] = time();
                    $result        = $srvObj->updateById($id, $data);
                } else {
                    $data["ctime"] = time();
                    $result        = $srvObj->insert($data);
                }
                // 判断是否成功
                if ($result === false) {
                    //失败回滚
                    $connection->rollback();
                    return -2;
                }
            }
        }

        // 提交事务
        $connection->commit();

        return 1;
    }
}
