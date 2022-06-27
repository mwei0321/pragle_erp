-- 是否同步 1是,0否,-1,不需要
-- 企业
ALTER TABLE `tbenterprise` 
ADD COLUMN `sync_id` int(11) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `user_del_type`;
-- 企业用户员工
ALTER TABLE `tbUser` 
ADD COLUMN `sync_id` int(11) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `updated_at`;
-- 企业用户员工详情
ALTER TABLE `tbUserinfo` 
ADD COLUMN `sync_id` int(11) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `Mobile`;
ALTER TABLE `tbrole` 
ADD COLUMN `sync_id` int(11) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `id`;
ALTER TABLE `tbgroup` 
ADD COLUMN `sync_id` int(11) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `id`;

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
ALTER TABLE `tbVedio` 
ADD COLUMN `sync_id` int(11) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `Vid`;
ALTER TABLE `tbmakeaddver` 
ADD COLUMN `sync_id` int(11) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `id`;
ALTER TABLE `tbplaybase` 
ADD COLUMN `sync_id` int(11) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `Pid`;
ALTER TABLE `tbplaylist` 
ADD COLUMN `sync_id` int(11) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `Pid`;
ALTER TABLE `tbplaylistplan` 
ADD COLUMN `sync_id` int(11) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `Pid`;
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
ALTER TABLE `tbanalysis` 
ADD COLUMN `sync_id` int(11) NULL DEFAULT 0 COMMENT '同步id,-1,不需要' AFTER `id`;




-- to 新增
ALTER TABLE `dbcenter_to`.`tbdevicestatus` 
ADD COLUMN `access` int(255) NULL DEFAULT 0 COMMENT '0进,1出,2进出' AFTER `face_flag`;

ALTER TABLE `tbUser` 
ADD COLUMN `delete_state` tinyint(1) NULL DEFAULT 0 COMMENT '考勤用户是否要删除，0否，1是' AFTER `sync_id`;




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



-- sync_from
UPDATE tbUser SET sync_id=0;
UPDATE tbenterprise SET sync_id=0;

--  sync_to
SELECT uid,`username`,role_id FROM tbUser	ORDER BY uid desc limit 4;
SELECT * FROM tbrole ORDER BY id desc LIMIT 5;
SELECT * FROM tbgroup ORDER BY id desc LIMIT 5;
SELECT * FROM tbDevice WHERE Devno="38A194E99D2D7AD9";
SELECT * FROM tbdevicestatus WHERE devicenum = "38A194E99D2D7AD9";
-- sync_delete_test
DELETE FROM tbUser WHERE uid in (SELECT * FROM (SELECT uid FROM tbUser ORDER BY uid desc LIMIT 2) s);
DELETE FROM tbUserinfo WHERE id in (SELECT * FROM (SELECT id FROM tbUserinfo ORDER BY id desc LIMIT 2)s);
DELETE FROM tbDevice WHERE Devno="38A194E99D2D7AD9";
DELETE FROM tbdevicestatus WHERE devicenum = "38A194E99D2D7AD9";
DELETE FROM tbpushrec WHERE Devno="38A194E99D2D7AD9";
DELETE FROM tbwallet WHERE wallet_id in (SELECT * FROM (SELECT wallet_id FROM tbwallet ORDER BY wallet_id desc LIMIT 2)s)




TRUNCATE TABLE `tborder`;
TRUNCATE TABLE `tbenterprise`;
TRUNCATE TABLE `tbUser`;
TRUNCATE TABLE `tbUserinfo`;
TRUNCATE TABLE `tbDevice`;
TRUNCATE TABLE `tbcontrollist`;
TRUNCATE TABLE `tbdeviceflow`;
TRUNCATE TABLE `tbDevicePay`;
TRUNCATE TABLE `tbdevicestatus`;
TRUNCATE TABLE `tbdevicelimit`;
TRUNCATE TABLE `tbmakeaddver`;
TRUNCATE TABLE `tbVedio`;
TRUNCATE TABLE `tbanalysis`;
TRUNCATE TABLE `tbpushrec`;
TRUNCATE TABLE `tbstock`;
TRUNCATE TABLE `tborder`;
TRUNCATE TABLE `tborder_item`;
TRUNCATE TABLE `tborder_log`;
TRUNCATE TABLE `tbwallet`;
TRUNCATE TABLE `tbwallet_log`;
TRUNCATE TABLE `tbflowcord`;
TRUNCATE TABLE `tbflowrecord`;
TRUNCATE TABLE `tbStatisticsDevice`;



INSERT INTO sync_play (from_uid,to_uid,to_enterprise_id,to_parent_enterprise) SELECT * FROM (SELECT u.sync_id from_uid,u.uid to_uid, u.Company_id to_enterprise_id,e.ParentID to_parent_enterprise FROM tbenterprise e LEFT JOIN tbUser u ON u.Company_id=e.id WHERE e.sync_id > 0 AND u.Company_id > 0) AS s 