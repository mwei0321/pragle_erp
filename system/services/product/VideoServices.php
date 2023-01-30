<?php

/* 产品服务
 * @Author: MaWei 
 * @Date: 2022-01-06 10:35:44 
 * @Last Modified by: MaWei
 * @Last Modified time: 2023-01-30 14:21:29
 */

namespace system\services\product;

use yii\db\Query;
use system\common\{TableMap, ServiceFactory};
use system\beans\goods\VideoBeans;

class VideoServices
{
    /**
     * 视频列表
     *
     * @param  VideoBeans $videoBeans
     * date: 2023-01-30 14:12:14
     * @author  <mawei.live>
     * @return void
     */
    function getList(VideoBeans $videoBeans)
    {

        // 构建查询
        $query = (new Query())->from(TableMap::Video)->where(["is_del" => 0]);

        // 分组
        if ($videoBeans->group_id > 0) {
            $query->where("group_id", $videoBeans->group_id);
        }

        // 企业
        if ($videoBeans->enterprise_id > 0) {
            $query->where("enterprise_id", $videoBeans->enterprise_id);
        }

        // 状态 0. 禁用 1.启用
        if ($videoBeans->status) {
            $query->andWhere(['status' => $videoBeans->status]);
        }

        // 语言
        if ($videoBeans->language) {
            $query->where("FIND_IN_SET(" . $videoBeans->language . ",`language`)");
        }

        $count = $query->count();
        // 分页
        $videoBeans->page($count);

        // 排序提取
        $list = $query->orderBy("id DESC")
            ->limit($videoBeans->limit)
            ->offset($videoBeans->offset)
            ->all();

        return $list;
    }
}
