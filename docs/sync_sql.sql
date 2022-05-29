-- 是否同步 1是,0否,-1,不需要
-- 企业
ALTER TABLE `tbenterprise` 
ADD COLUMN `sync_id` tinyint(1) NULL DEFAULT 0 COMMENT ' 是否同步了' AFTER `user_del_type`;
-- 企业用户员工
ALTER TABLE `tbUser` 
ADD COLUMN `sync_id` tinyint(1) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `updated_at`;
-- 企业用户员工详情
ALTER TABLE `tbuserinfo` 
ADD COLUMN `sync_id` tinyint(1) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `Mobile`;

-- 设备
ALTER TABLE `tbDevice` 
ADD COLUMN `sync_id` tinyint(1) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `Company_id`;
ALTER TABLE `tbcontrollist` 
ADD COLUMN `sync_id` tinyint(1) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `Company_id`;
ALTER TABLE `tbdeviceflow` 
ADD COLUMN `sync_id` tinyint(1) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `Company_id`;
ALTER TABLE `tbDevicePay` 
ADD COLUMN `sync_id` tinyint(1) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `company_id`;
ALTER TABLE `tbdevicestatus` 
ADD COLUMN `sync_id` tinyint(1) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `id`;
ALTER TABLE `tbdevicelimit` 
ADD COLUMN `sync_id` tinyint(1) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `Company_id`;

-- 节目
ALTER TABLE `tbmakeaddver` 
ADD COLUMN `sync_id` tinyint(1) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `Company_id`;
ALTER TABLE `tbanalysis` 
ADD COLUMN `sync_id` tinyint(1) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `Company_id`;

-- 订单
ALTER TABLE `tborder` 
ADD COLUMN `sync_id` tinyint(1) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `order_id`;
ALTER TABLE `tborder_item` 
ADD COLUMN `sync_id` tinyint(1) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `order_id`;
ALTER TABLE `tborder_log` 
ADD COLUMN `sync_id` tinyint(1) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `order_id`;
ALTER TABLE `tbwallet` 
ADD COLUMN `sync_id` tinyint(1) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `Company_id`;
ALTER TABLE `tbwallet_log` 
ADD COLUMN `sync_id` tinyint(1) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `Company_id`;

-- 流量统计
ALTER TABLE `tbflowcord` 
ADD COLUMN `sync_id` tinyint(1) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `Company_id`;
ALTER TABLE `tbflowrecord` 
ADD COLUMN `sync_id` tinyint(1) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `Company_id`;
ALTER TABLE `tbStatisticsDevice` 
ADD COLUMN `sync_id` tinyint(1) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `devno`;


--  同步进入库
-- 企业用户员工
ALTER TABLE `tbenterprise` 
ADD COLUMN `sync_id` int NULL DEFAULT 0 COMMENT '同步id' AFTER `user_del_type`;
ALTER TABLE `tbUser` 
ADD COLUMN `sync_id` int NULL DEFAULT 0 COMMENT '同步id' AFTER `updated_at`;
ALTER TABLE `tbuserinfo` 
ADD COLUMN `sync_id` int NULL DEFAULT 0 COMMENT '同步id' AFTER `Mobile`;
ALTER TABLE `tbDevice` 
ADD COLUMN `sync_id` int NULL DEFAULT 0 COMMENT '同步id' AFTER `Company_id`;
-- 设备
ALTER TABLE `tbDevice` 
ADD COLUMN `sync_id` int NULL DEFAULT 0 COMMENT '同步id' AFTER `Company_id`;
ALTER TABLE `tbcontrollist` 
ADD COLUMN `sync_id` int NULL DEFAULT 0 COMMENT '同步id' AFTER `Company_id`;
ALTER TABLE `tbdeviceflow` 
ADD COLUMN `sync_id` int NULL DEFAULT 0 COMMENT '同步id' AFTER `Company_id`;
ALTER TABLE `tbDevicePay` 
ADD COLUMN `sync_id` int NULL DEFAULT 0 COMMENT '同步id' AFTER `company_id`;
ALTER TABLE `tbdevicestatus` 
ADD COLUMN `sync_id` int NULL DEFAULT 0 COMMENT '同步id' AFTER `id`;
ALTER TABLE `tbdevicelimit` 
ADD COLUMN `sync_id` int NULL DEFAULT 0 COMMENT '同步id' AFTER `Company_id`;