<?php


namespace system\beans\project;

use system\common\BaseBean;

class ProjectBeans extends BaseBean
{
    public $id                     = 0;
    public $staff_id               = []; // 负责人id
    public $enterprise_id          = 0; // 负责人企业id
    public $customer_id            = 0; // 顾客联系人id
    public $customer_enterprise_id = 0; // 顾客企业id
    public $name                   = ''; // 项目名称
    public $imgs                   = ''; // 项目图片
    public $series_id              = 0; // 系列ID
    public $modal_id               = 0; // 型号ID
    public $start_at               = 0; // 项目开始时间
    public $end_at                 = 0; // 项目结束时间
    public $follow_times           = 0; // 跟进次数
    public $level                  = 0; // 项目级别，1重大，2重点，3重要，4普通
    public $state                  = 0; // 状态，1成交，2丢失，3跟进中，4无回应，5项目推迟
    public $product_id             = 0; // 关联产品
    public $area                   = 0; // 产品面积
    public $description            = ''; // 描述
    public $appendix               = ''; // 附件
    public $create_at              = 0; // 创建时间
}
