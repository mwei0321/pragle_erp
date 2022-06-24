<?php
/*
 * @Author: MaWei 1123265518@qq.com
 * @Date: 2022-05-23 09:42:09
 * @LastEditors: MaWei 1123265518@qq.com
 * @LastEditTime: 2022-06-24
 * @FilePath: \Pragle_erp\system\beans\sync\SyncBaseBeans.php
 * @Description: 数据同步基础参数
 * 
 * Copyright (c) 2022 by MaWei 1123265518@qq.com, All Rights Reserved. 
 */


namespace system\beans\sync;

use system\common\BaseBean;

class SyncBaseBeans extends BaseBean
{
    // 原参数
    public $from_id                = 0;
    public $from_parent_enterpirse = 0;
    public $from_enterprise_id     = 0;
    public $from_uid               = 0;
    public $from_device_id         = 0;
    public $from_order_id          = 0;
    public $from_device_no         = 0;
    public $from_role_id           = 0;
    public $from_group_id          = 0;
    public $from_play_id           = 0; // 旧播放id
    public $from_ad_id             = 0; // 旧节目id

    // 新参数
    public $to_id                  = 0;
    public $to_enterprise_id       = 0;
    public $to_parent_enterpirse   = 0;
    public $to_uid                 = 0;
    public $to_device_id           = 0;
    public $to_order_id            = 0;
    public $to_device_no           = 0;
    public $to_role_id             = 0;
    public $to_group_id            = 0;
    public $to_play_id             = 0; // 旧播放id
    public $to_ad_id               = 0; // 新节目id
    // 不变
    public $is_main                = 0;

    public $page_size              = 30;
}
