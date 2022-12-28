<?php

/* 项目跟进
 * @Author: MaWei 
 * @Date: 2022-12-28 09:41:27 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-12-28 14:37:03
 */

namespace system\services\project;

use yii\db\Query;
use system\common\{TableMap, ServiceFactory};
use system\beans\project\ProjectBeans;

class ProjectServices
{

    function getList(ProjectBeans $projectBeans)
    {

        // 构建查询
        $query = (new Query())->from(TableMap::Project);

        // 跟进员工
        if ($projectBeans->staff_id > 0) {
            $query->where("staff_id", $projectBeans->staff_id);
        }

        // 跟进状态 1成交，2丢失，3跟进中，4无回应，5项目推迟
        if ($projectBeans->state) {
            $query->andWhere(['state' => $projectBeans->state]);
        }

        // 客户企业搜索
        if ($projectBeans->customer_enterprise_id > 0) {
            $query->andWhere(['customer_enterprise_id' => $projectBeans->customer_enterprise_id]);
        }

        // 关注产品
        if ($projectBeans->product_id) {
            $query->andWhere(['product_id' => $projectBeans->product_id]);
        }

        $count = $query->count();
        // 分页
        $projectBeans->page($count);

        // 排序提取
        $list = $query->orderBy("id DESC")
            ->limit($projectBeans->limit)
            ->offset($projectBeans->offset)
            ->all();

        // 企业
        // foreach ($list as $k => $v) {
        //     $list[$k]['enterprise'] = ServiceFactory::getInstance("BaseDB", TableMap::Enterprise)->getInfoById($v["buyer_enterprise_id"], "id,fullname,shortname");
        // }

        return $list;
    }
}
