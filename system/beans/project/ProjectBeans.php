<?php


namespace system\beans\project;

use system\common\BaseBean;

class ProjectBeans extends BaseBean
{
    public $id                     = 0;
    public $staff_id               = 0; // 负责人id
    public $enterprise_id          = 0; // 负责人企业id
    public $customer_id            = 0; // 顾客联系人id
    public $customer_enterprise_id = 0; // 顾客企业id
    public $name                   = ''; // 项目名称
    public $country                = ''; // 国家
    public $province               = ''; // 省
    public $city                   = ''; // 市/区
    public $district               = ''; // 县
    public $start_at               = ''; // 项目开始时间
    public $end_at                 = ''; // 项目结束时间
    public $follow_times           = ''; // 跟进次数
    public $level                  = ''; // 项目级别，1重大，2重点，3重要，4普通
    public $state                  = ''; // 状态，1成交，2丢失，3跟进中，4无回应，5项目推迟
    public $product_id             = 0; // 关联产品
    public $amount                 = ''; // 项目金额，单位为万
    public $product_area           = ''; // 产品面积
    public $description            = ''; // 描述
}
