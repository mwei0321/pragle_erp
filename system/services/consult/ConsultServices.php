<?php
/*
 * @Author: MaWei 1123265518@qq.com
 * @Date: 2022-06-29
 * @LastEditors: MaWei 1123265518@qq.com
 * @LastEditTime: 2022-06-29
 * @FilePath: \Pragle_erp\system\services\consult\ConsultServices.php
 * @Description: 联系
 * 
 * Copyright (c) 2022 by MaWei 1123265518@qq.com, All Rights Reserved. 
 */

namespace system\services\consult;

use yii\db\Query;
use system\common\{TableMap, ServiceFactory};
use system\beans\consult\ConsultBeans;

class ConsultServices
{

    /**
     * 联系我们
     * @param  \system\beans\consult\ConsultBeans $consultBeans
     * date: 2022-06-29 20:18:11
     * @author  <mawei.live>
     * @return void
     */
    function getConsultList(ConsultBeans $consultBeans)
    {
        // 字段
        $field = '*';

        // 构建条件
        $query = (new Query())->select($field)
            ->from(TableMap::Consult);

        // 部门
        if ($consultBeans->keyword) {
            $query->andWhere([
                "or",
                ["company_name", "%" . $consultBeans->keyword . "%"],
                ["email", "%" . $consultBeans->keyword . "%"],
                ["phone", "%" . $consultBeans->keyword . "%"],
            ]);
        }

        // 总条数
        $count = $query->count();
        if ($count < 1) {
            return [];
        }
        // 分页 
        $consultBeans->page($count);

        // 排序提取
        $list = $query->orderBy("utime DESC")
            ->limit($consultBeans->limit)
            ->offset($consultBeans->offset)
            ->all();

        return $list;
    }
}
