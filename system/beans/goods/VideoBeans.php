<?php
/*
* @Author: MaWei 1123265518@qq.com
* @Date: 2022-07-04
* @LastEditors: MaWei 1123265518@qq.com
* @LastEditTime: 2022-07-04
* @FilePath: \Pragle_erp\system\beans\goods\VideoBeans.php
* @Description:
*
* Copyright (c) 2022 by MaWei 1123265518@qq.com, All Rights Reserved.
*/

namespace system\beans\goods;

use system\common\BaseBean;

class VideoBeans extends BaseBean
{
    public $id = 0;
    public $enterprise_id = 0; // 企业id
    public $staff_id = 0; // 上传员工id
    public $group_id = 0; // 分组
    public $title = 0; // 标题
    public $publish_time = 0; // 发布时间
    public $status = 0; // 状态 0. 禁用 1.启用
    public $language = ""; // 语言类型
    public $keyword = ""; // 关键字
    public $intro = ""; // 简介
    public $url = ""; // 视频地址
    public $utime = 0; // 更新时间
    public $ctime = 0; // 创建时间
}
