<?php

/**
 * 表名定义
 * @Author: MaWei
 * @Date:   2021-10-26
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-02-19 21:04:45
 */

namespace system\common;

class TableMap
{
	// erp 库
	const Config                  = 'config';
	const Enterprise              = 'enterprise';
	const EnterpriseMarketingKpi  = 'enterprise_marketing_kpi';
	const DepartmentActionKpi     = 'department_action_kpi';
	const Staff                   = 'user';
	const ActionFollow            = "action_follow";
	const FollowScoreLog          = "action_follow_score_log";
	const ActionDayStatisticsLog  = "action_day_statistics_log";
	const MarketDayStatisticsLog  = "market_day_statistics_log";
	const ActionCronLog           = "action_cron_log";
	const StaffDepartment         = 'user_department';
	const StaffActionKpi          = 'staff_action_kpi';
	const StaffMarketingKpi       = 'staff_marketing_kpi';
	const DepartmentMarketingKpi  = 'department_marketing_kpi';
	const DepartmentAndStaffScore = 'department_and_staff_score';
	const Group                   = 'group';
	const Department              = 'group';
	const GroupMember             = 'group_member';
	const FollowInfo              = "follow_info";
	const User                    = "user";
	const Order                   = "order";
	const OrderDetail             = "order_detail";
	const Product                 = "product";
	const ActionYear              = "action_year";

	// dbDate 库
	const TaskQueue               = "task_queue";
	const TaskDistribute          = "task_distribute";


	// dbcenter库
	// 企业用户
	const TbEnterprise            = "tbenterprise";
	const TbUser                  = "tbUser";
	const TbUserInfo              = "tbUserinfo";
	const TbGroup = "tbgroup";
	const TbRole = "tbrole";
	// 设备
	const TbDevice                = "tbDevice";
	const TbDeviceFlow            = "tbdeviceflow";
	const TbDevicePay             = "tbDevicePay";
	const TbDeviceLimit           = "tbdevicelimit";
	const TbDeviceStatus          = "tbdevicestatus";
	const TbControllerList        = "tbcontrollist";
	// 素材
	const TbVedio                 = "tbVedio";
	const TbAnalysis              = "tbanalysis";
	// 节目
	const TbMakeAddver            = "tbmakeaddver"; // 节日主表 1
	const TbPlayBase              = "tbplaybase"; 
	const TbPlayList              = "tbplaylist"; // 播放列表
	const TbPlayListPlan          = "tbplaylistplan";  // 播放计划
	const AdvDownload             = "adv_download"; // 下载记录
	const TbPushRec               = "tbpushrec";
	// 购买支付
	const TbStock                 = "tbstock";
	const TbOrder                 = "tborder";
	const TbOrderItem             = "tborder_item";
	const TbOrderLog              = "tborder_log";
	const TbWallet                = "tbwallet";
	const TbWalletLog             = "tbwallet_log";
	// 流量统计
	const TbFlowCord              = "tbflowcord";
	const TbFlowRecord            = "tbflowrecord";
	const TbStatisticsDevice      = "tbStatisticsDevice";
	const UserActive              = "user_active";
}
