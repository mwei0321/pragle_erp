-- 是否同步 1是,0否,-1,不需要
-- 企业
ALTER TABLE `tbenterprise` 
ADD COLUMN `sync_id` int(11) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `user_del_type`;
-- 企业用户员工
ALTER TABLE `tbUser` 
ADD COLUMN `sync_id` int(11) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `updated_at`;
-- 企业用户员工详情
ALTER TABLE `tbuserinfo` 
ADD COLUMN `sync_id` int(11) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `Mobile`;

-- 设备
ALTER TABLE `tbDevice` 
ADD COLUMN `sync_id` int(11) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `Company_id`;
ALTER TABLE `tbcontrollist` 
ADD COLUMN `sync_id` int(11) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `Company_id`;
ALTER TABLE `tbdeviceflow` 
ADD COLUMN `sync_id` int(11) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `Company_id`;
ALTER TABLE `tbDevicePay` 
ADD COLUMN `sync_id` int(11) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `company_id`;
ALTER TABLE `tbdevicestatus` 
ADD COLUMN `sync_id` int(11) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `id`;
ALTER TABLE `tbdevicelimit` 
ADD COLUMN `sync_id` int(11) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `Company_id`;

-- 节目
ALTER TABLE `tbmakeaddver` 
ADD COLUMN `sync_id` int(11) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `Company_id`;
ALTER TABLE `tbVedio` 
ADD COLUMN `sync_id` int(11) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `Company_id`;
ALTER TABLE `tbanalysis` 
ADD COLUMN `sync_id` int(11) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `Company_id`;
ALTER TABLE `tbpushrec` 
ADD COLUMN `sync_id` int(11) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `Devno`;

-- 订单
ALTER TABLE `tbstock` 
ADD COLUMN `sync_id` int(11) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `Company_id`;
ALTER TABLE `tborder` 
ADD COLUMN `sync_id` int(11) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `order_id`;
ALTER TABLE `tborder_item` 
ADD COLUMN `sync_id` int(11) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `order_id`;
ALTER TABLE `tborder_log` 
ADD COLUMN `sync_id` int(11) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `order_id`;
ALTER TABLE `tbwallet` 
ADD COLUMN `sync_id` int(11) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `Company_id`;
ALTER TABLE `tbwallet_log` 
ADD COLUMN `sync_id` int(11) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `Company_id`;

-- 流量统计
ALTER TABLE `tbflowcord` 
ADD COLUMN `sync_id` int(11) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `Company_id`;
ALTER TABLE `tbflowrecord` 
ADD COLUMN `sync_id` int(11) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `Company_id`;
ALTER TABLE `tbStatisticsDevice` 
ADD COLUMN `sync_id` int(11) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `devno`;




-- to 新增
ALTER TABLE `dbcenter_to`.`tbdevicestatus` 
ADD COLUMN `access` int(255) NULL DEFAULT 0 COMMENT '0进,1出,2进出' AFTER `face_flag`;




ALTER TABLE `tborder` DROP COLUMN `sync_id`;
ALTER TABLE `tbenterprise` DROP COLUMN `sync_id`;
ALTER TABLE `tbUser` DROP COLUMN `sync_id`;
ALTER TABLE `tbuserinfo` DROP COLUMN `sync_id`;
ALTER TABLE `tbDevice` DROP COLUMN `sync_id`;
ALTER TABLE `tbcontrollist` DROP COLUMN `sync_id`;
ALTER TABLE `tbdeviceflow` DROP COLUMN `sync_id`;
ALTER TABLE `tbDevicePay` DROP COLUMN `sync_id`;
ALTER TABLE `tbdevicestatus` DROP COLUMN `sync_id`;
ALTER TABLE `tbdevicelimit` DROP COLUMN `sync_id`;
ALTER TABLE `tbmakeaddver` DROP COLUMN `sync_id`;
ALTER TABLE `tbVedio` DROP COLUMN `sync_id`;
ALTER TABLE `tbanalysis` DROP COLUMN `sync_id`;
ALTER TABLE `tbpushrec` DROP COLUMN `sync_id`;
ALTER TABLE `tbstock` DROP COLUMN `sync_id`;
ALTER TABLE `tborder` DROP COLUMN `sync_id`;
ALTER TABLE `tborder_item` DROP COLUMN `sync_id`;
ALTER TABLE `tborder_log` DROP COLUMN `sync_id`;
ALTER TABLE `tbwallet` DROP COLUMN `sync_id`;
ALTER TABLE `tbwallet_log` DROP COLUMN `sync_id`;
ALTER TABLE `tbflowcord` DROP COLUMN `sync_id`;
ALTER TABLE `tbflowrecord` DROP COLUMN `sync_id`;
ALTER TABLE `tbStatisticsDevice` DROP COLUMN `sync_id`;