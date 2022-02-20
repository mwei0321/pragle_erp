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
	const ActionFollowLog         = "action_follow_log";
	const ActionDayStatisticsLog  = "action_day_statistics_log";
	const ActionCronLog           = "action_cron_log";
	const StaffDepartment         = 'user_department';
	const StaffActionKpi          = 'staff_action_kpi';
	const StaffMarketingKpi       = 'staff_marketing_kpi';
	const DepartmentMarketingKpi  = 'department_marketing_kpi';
	const DepartmentAndStaffScore = 'department_and_staff_score';
	const Group                   = 'group';
	const Department              = 'group';
	const GroupMember             = 'group_member';
	const Follow                  = "follow_info";
	const User                    = "user";

	// dbDate 库
	const TaskQueue               = "task_queue";
	const TaskDistribute          = "task_distribute";
}
