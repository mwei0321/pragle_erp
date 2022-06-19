<?php

/**
 * 同步企业用户
 * @Author: MaWei 
 * @Date: 2022-04-09 20:11:22 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-06-19 19:03:33
 */

namespace system\services\syncdata;

use system\beans\sync\SyncBaseBeans;
use system\common\TableMap;
use yii\db\Query;
use system\services\syncdata\SyncBaseSrv;

class EnterpriseUserSrv extends SyncBaseSrv
{

    /**
     * 同步企业
     * date: 2022-05-22 00:36:39
     * @author  <mawei.live>
     * @return void
     */
    function syncEnterpriseById(SyncBaseBeans $syncBaseBeans)
    {
        // 查询信息
        $info = (new Query())->from(TableMap::TbEnterprise)
            ->where([
                'id'      => $syncBaseBeans->from_enterprise_id,
                "sync_id" => 0,
            ])->one($this->syncFromDB);
        if (!$info || !isset($info['id'])) {
            return -1;
        }

        $info['sync_id'] = $oldId = $info['id'];
        unset($info['id']);
        unset($info['auto_power_off']);
        // 待确认
        unset($info['user_del_type']);

        // 同步信息
        $result = $this->syncToDB->createCommand()->insert(TableMap::TbEnterprise, $info)->execute();
        if ($result === false) {
            return -2;
        }
        //返回ID
        $newId = $this->syncToDB->getLastInsertID();
        // 同步回写
        if ($this->syncFromDB->createCommand()->update(TableMap::TbEnterprise, ["sync_id" => $newId], ['id' => $oldId])->execute() === false) {
            return -3;
        }

        $syncBaseBeans->to_enterprise_id = $newId;

        return 1;
    }

    /**
     * 同步用户信息
     * @param  array $_uidArr
     * @param  int $_enterpriseId
     * date: 2022-05-22 00:53:54
     * @author  <mawei.live>
     * @return bool
     */
    function syncUserInfoById(SyncBaseBeans $syncBaseBeans)
    {
        // 查询企业员工
        $list = (new Query())->from(TableMap::TbUser)
            ->where([
                'uid' => $syncBaseBeans->from_uid,
            ])
            ->all($this->syncFromDB);
        if (count($list) < 1) {
            return 1;
        }
        foreach ($list as $val) {
            $oldUid = 0;
            $val['Company_id'] = $syncBaseBeans->to_enterprise_id;
            $val['sync_id']  = $oldUid  = $val['uid'];
            $syncBaseBeans->is_main = $val['maincontact'];
            unset($val['uid']);
            $result = $this->syncToDB->createCommand()
                ->insert(TableMap::TbUser, $val)
                ->execute();
            if ($result === false) {
                return -1;
            }
            //返回ID
            $newUid = $this->syncToDB->getLastInsertID();
            $syncBaseBeans->to_uid = $newUid;

            // 更新同步id
            $result = $this->syncFromDB->createCommand()->update(TableMap::TbUser, ["sync_id" => $newUid], ['uid' => $oldUid])->execute();
            if ($result === false) {
                return -2;
            }

            // 权限组，角色处理
            $syncBaseBeans->from_role_id = $val['role_id'];
            $result = $this->SyncUserRoleAuth($syncBaseBeans);
            if ($result > 0) {
                echo "同步用户ID:" . $oldUid . "权限成功！";
            }

            // 查询企业员工详情
            $userDetail = (new Query())->from(TableMap::TbUserInfo)->where(['uid' => $syncBaseBeans->from_uid])->one($this->syncFromDB);
            $userDetail['uid']        = $newUid;
            $userDetail['sync_id']  = $oldId  = $userDetail['id'];
            unset($userDetail['id']);
            // 同步企业员工详情
            $result = $this->syncToDB->createCommand()
                ->insert(TableMap::TbUserInfo, $userDetail)
                ->execute();
            if ($result === false) {
                return -3;
            }
            $newUid = $this->syncToDB->getLastInsertID();

            // 更新同步
            $result = $this->syncFromDB->createCommand()->update(TableMap::TbUserInfo, ["sync_id" => $newUid], ['uid' => $oldId])->execute();
            if ($result === false) {
                return -4;
            }
        }

        return 1;
    }

    /**
     * 同步权限
     * 
     */
    function SyncUserRoleAuth(SyncBaseBeans $syncBaseBeans)
    {
        // 查询企业的默认权限组
        $role = (new Query())->from(TableMap::TbRole)
            ->where([
                'id' => $syncBaseBeans->from_role_id,
            ])
            ->one($this->syncFromDB);
        if (!$role) {
            return -1;
        }
        // 组
        $group = (new Query())->from(TableMap::TbGroup)
            ->where([
                'id' => $role['group_id'],
            ])
            ->one($this->syncFromDB);
        // 同步组
        $syncBaseBeans->from_group_id = $group['id'];
        $group['sync_id'] = $syncBaseBeans->from_group_id;
        $group['Company_id'] = $syncBaseBeans->to_enterprise_id;
        $group['uid'] = $syncBaseBeans->to_uid;
        unset($group['id']);
        $result = $this->syncToDB->createCommand()
            ->insert(TableMap::TbGroup, $group)
            ->execute();
        if ($result === false) {
            return -2;
        }
        $syncBaseBeans->to_group_id = $this->syncToDB->getLastInsertID();
        // 更新组同步id
        $result = $this->syncFromDB->createCommand()->update(TableMap::TbGroup, ["sync_id" => $syncBaseBeans->to_group_id], ['id' => $syncBaseBeans->from_group_id])->execute();
        if ($result === false) {
            return -3;
        }

        // 同步role
        $syncBaseBeans->from_role_id = $role['id'];
        $role['sync_id'] = $syncBaseBeans->from_role_id;
        $role['group_id'] = $syncBaseBeans->to_group_id;
        $role['parent_id'] = $syncBaseBeans->to_enterprise_id;
        $role['is_default'] = $syncBaseBeans->is_main;
        $role['node'] = $this->node;
        $role['item'] = $this->item;
        $role['total'] = $this->total;
        unset($role['id']);
        $result = $this->syncToDB->createCommand()
            ->insert(TableMap::TbRole, $role)
            ->execute();
        if ($result === false) {
            return -4;
        }
        $syncBaseBeans->to_role_id = $this->syncToDB->getLastInsertID();
        // 更新组同步id
        $result = $this->syncFromDB->createCommand()->update(TableMap::TbRole, ["sync_id" => $syncBaseBeans->to_role_id], ['id' => $syncBaseBeans->from_role_id])->execute();
        if ($result === false) {
            return -5;
        }

        // 更新用户的权限
        $result = $this->syncToDB->createCommand()->update(TableMap::TbUser, ["role_id" => $syncBaseBeans->to_role_id], ['uid' => $syncBaseBeans->to_uid])->execute();
        if ($result === false) {
            return -5;
        }

        return 1;
    }

    /**
     * 查询企业员工同步用户信息
     * @param  array $_uidArr
     * @param  int $_enterpriseId
     * date: 2022-05-22 00:53:54
     * @author  <mawei.live>
     * @return bool
     */
    function getEnterpriseSyncUserId(SyncBaseBeans $syncBaseBeans)
    {
        // 查询企业员工
        $list = (new Query())->from(TableMap::TbUser)
            ->select("uid")
            ->where([
                'Company_id' => $syncBaseBeans->from_enterprise_id,
                "sync_id"    => 0,
            ])
            ->all($this->syncFromDB);

        return $list ? array_column($list, "uid") : [];
    }

    /**
     * 同步统计记录
     * @param  \system\beans\sync\SyncBaseBeans $syncBaseBeans
     * date: 2022-06-01 11:15:47
     * @author  <mawei.live>
     * @return void
     */
    function syncUserActiveByEnterpriseId(SyncBaseBeans $syncBaseBeans)
    {
        // 查询信息
        $info = (new Query())->from(TableMap::UserActive)
            ->where([
                'company_id'      => $syncBaseBeans->from_enterprise_id,
            ])->one($this->syncFromDB);
        if (!$info || isset($info['id'])) {
            echo "来源企业没有查询到:" . $syncBaseBeans->from_enterprise_id;
            return -1;
        }
        $info['sync_id'] = $oldId = $info['id'];
        unset($info['id']);

        // 同步信息
        $result = $this->syncToDB->createCommand()->insert(TableMap::UserActive, $info)->execute();
        if ($result === false) {
            echo "同步来源企业失败:" . json_encode($result);
            return -2;
        }
        //返回ID
        $newId = $this->syncToDB->getLastInsertID();

        // 同步回写
        if ($result = $this->syncFromDB->createCommand()->update(TableMap::UserActive, ["sync_id" => $newId], ['id' => $oldId])->execute() === false) {
            echo "同步回写来源企业失败:" . json_encode($result);
            return -3;
        }

        return 1;
    }

    /**
     * 查询email是否存在
     * @param  array $_emailArr
     * date: 2022-05-22 00:11:38
     * @author  <mawei.live>
     * @return bool
     */
    function checkEmailIsExist($_emailArr)
    {
        $info = (new Query())->from(TableMap::TbUserInfo)
            ->select("uid")
            ->where([
                "in", "Email", $_emailArr
            ])
            ->one($this->syncToDB);

        return isset($info['uid']) && $info['uid'] > 0 ? true : false;
    }

    // 构造函数
    function __construct()
    {
        parent::__construct();
    }

    public $node = 'a:269:{i:0;s:2:"27";i:1;s:3:"158";i:2;s:2:"39";i:3;s:2:"41";i:4;s:2:"84";i:5;s:3:"131";i:6;s:2:"51";i:7;s:3:"229";i:8;s:3:"236";i:9;s:3:"237";i:10;s:3:"238";i:11;s:3:"239";i:12;s:3:"240";i:13;s:3:"510";i:14;s:3:"511";i:15;s:3:"512";i:16;s:3:"514";i:17;s:3:"515";i:18;s:3:"135";i:19;s:3:"128";i:20;s:3:"153";i:21;s:2:"74";i:22;s:3:"192";i:23;s:3:"210";i:24;s:3:"235";i:25;s:3:"277";i:26;s:3:"154";i:27;s:2:"58";i:28;s:2:"59";i:29;s:2:"60";i:30;s:2:"61";i:31;s:2:"62";i:32;s:2:"63";i:33;s:2:"64";i:34;s:2:"65";i:35;s:3:"105";i:36;s:3:"109";i:37;s:3:"110";i:38;s:3:"112";i:39;s:3:"127";i:40;s:3:"179";i:41;s:3:"180";i:42;s:3:"181";i:43;s:3:"278";i:44;s:3:"518";i:45;s:3:"519";i:46;s:3:"520";i:47;s:3:"521";i:48;s:3:"177";i:49;s:3:"178";i:50;s:3:"200";i:51;s:3:"231";i:52;s:3:"232";i:53;s:3:"233";i:54;s:3:"234";i:55;s:3:"522";i:56;s:3:"136";i:57;s:2:"12";i:58;s:3:"103";i:59;s:3:"175";i:60;s:3:"207";i:61;s:3:"137";i:62;s:1:"8";i:63;s:1:"9";i:64;s:3:"241";i:65;s:3:"242";i:66;s:3:"243";i:67;s:3:"244";i:68;s:3:"138";i:69;s:2:"71";i:70;s:2:"72";i:71;s:2:"75";i:72;s:2:"76";i:73;s:3:"245";i:74;s:3:"195";i:75;s:3:"196";i:76;s:3:"218";i:77;s:3:"230";i:78;s:3:"246";i:79;s:3:"525";i:80;s:3:"529";i:81;s:3:"133";i:82;s:3:"130";i:83;s:3:"167";i:84;s:3:"263";i:85;s:3:"264";i:86;s:3:"265";i:87;s:3:"266";i:88;s:3:"523";i:89;s:3:"226";i:90;s:3:"280";i:91;s:3:"281";i:92;s:3:"508";i:93;s:3:"509";i:94;s:3:"227";i:95;s:3:"267";i:96;s:3:"268";i:97;s:3:"247";i:98;s:3:"279";i:99;s:3:"139";i:100;s:3:"255";i:101;s:3:"261";i:102;s:3:"276";i:103;s:3:"155";i:104;s:3:"270";i:105;s:3:"271";i:106;s:3:"273";i:107;s:3:"274";i:108;s:3:"275";i:109;s:3:"285";i:110;s:3:"517";i:111;s:3:"524";i:112;s:3:"254";i:113;s:1:"5";i:114;s:1:"6";i:115;s:2:"11";i:116;s:2:"13";i:117;s:2:"14";i:118;s:2:"16";i:119;s:2:"20";i:120;s:2:"23";i:121;s:2:"52";i:122;s:2:"10";i:123;s:2:"45";i:124;s:2:"54";i:125;s:2:"67";i:126;s:2:"68";i:127;s:2:"69";i:128;s:2:"97";i:129;s:3:"117";i:130;s:3:"125";i:131;s:3:"126";i:132;s:2:"53";i:133;s:2:"70";i:134;s:3:"118";i:135;s:2:"82";i:136;s:2:"83";i:137;s:2:"87";i:138;s:3:"104";i:139;s:3:"121";i:140;s:3:"122";i:141;s:3:"123";i:142;s:3:"124";i:143;s:3:"152";i:144;s:3:"286";i:145;s:3:"287";i:146;s:3:"294";i:147;s:3:"295";i:148;s:3:"296";i:149;s:3:"302";i:150;s:3:"304";i:151;s:3:"305";i:152;s:3:"307";i:153;s:3:"308";i:154;s:3:"310";i:155;s:3:"311";i:156;s:3:"312";i:157;s:3:"313";i:158;s:3:"314";i:159;s:3:"315";i:160;s:3:"316";i:161;s:3:"317";i:162;s:3:"318";i:163;s:3:"319";i:164;s:3:"320";i:165;s:3:"321";i:166;s:3:"322";i:167;s:3:"323";i:168;s:3:"326";i:169;s:3:"327";i:170;s:3:"331";i:171;s:3:"332";i:172;s:3:"333";i:173;s:3:"334";i:174;s:3:"335";i:175;s:3:"340";i:176;s:3:"341";i:177;s:3:"342";i:178;s:3:"343";i:179;s:3:"344";i:180;s:3:"345";i:181;s:3:"346";i:182;s:3:"347";i:183;s:3:"348";i:184;s:3:"349";i:185;s:3:"361";i:186;s:3:"362";i:187;s:3:"363";i:188;s:3:"364";i:189;s:3:"369";i:190;s:3:"373";i:191;s:3:"374";i:192;s:3:"375";i:193;s:3:"376";i:194;s:3:"377";i:195;s:3:"378";i:196;s:3:"380";i:197;s:3:"382";i:198;s:3:"383";i:199;s:3:"384";i:200;s:3:"385";i:201;s:3:"386";i:202;s:3:"389";i:203;s:3:"390";i:204;s:3:"391";i:205;s:3:"392";i:206;s:3:"393";i:207;s:3:"394";i:208;s:3:"395";i:209;s:3:"396";i:210;s:3:"397";i:211;s:3:"398";i:212;s:3:"399";i:213;s:3:"400";i:214;s:3:"403";i:215;s:3:"404";i:216;s:3:"411";i:217;s:3:"412";i:218;s:3:"413";i:219;s:3:"414";i:220;s:3:"415";i:221;s:3:"416";i:222;s:3:"417";i:223;s:3:"419";i:224;s:3:"420";i:225;s:3:"421";i:226;s:3:"422";i:227;s:3:"423";i:228;s:3:"429";i:229;s:3:"434";i:230;s:3:"435";i:231;s:3:"436";i:232;s:3:"441";i:233;s:3:"442";i:234;s:3:"443";i:235;s:3:"445";i:236;s:3:"446";i:237;s:3:"448";i:238;s:3:"449";i:239;s:3:"451";i:240;s:3:"453";i:241;s:3:"455";i:242;s:3:"458";i:243;s:3:"459";i:244;s:3:"460";i:245;s:3:"461";i:246;s:3:"462";i:247;s:3:"463";i:248;s:3:"464";i:249;s:3:"465";i:250;s:3:"466";i:251;s:3:"467";i:252;s:3:"469";i:253;s:3:"470";i:254;s:3:"473";i:255;s:3:"480";i:256;s:3:"482";i:257;s:3:"483";i:258;s:3:"484";i:259;s:3:"485";i:260;s:3:"498";i:261;s:3:"507";i:262;s:3:"516";i:263;s:3:"527";i:264;s:3:"528";i:265;s:3:"530";i:266;s:3:"531";i:267;s:3:"532";i:268;s:3:"533";}';
    public $total = 'a:267:{i:0;s:2:"27";i:1;s:3:"158";i:2;s:2:"39";i:3;s:2:"41";i:4;s:2:"84";i:5;s:3:"131";i:6;s:2:"51";i:7;s:3:"229";i:8;s:3:"236";i:9;s:3:"237";i:10;s:3:"238";i:11;s:3:"239";i:12;s:3:"240";i:13;s:3:"510";i:14;s:3:"511";i:15;s:3:"512";i:16;s:3:"514";i:17;s:3:"515";i:18;s:3:"135";i:19;s:3:"128";i:20;s:3:"153";i:21;s:2:"74";i:22;s:3:"192";i:23;s:3:"210";i:24;s:3:"235";i:25;s:3:"277";i:26;s:3:"154";i:27;s:2:"58";i:28;s:2:"59";i:29;s:2:"60";i:30;s:2:"61";i:31;s:2:"62";i:32;s:2:"63";i:33;s:2:"64";i:34;s:2:"65";i:35;s:3:"105";i:36;s:3:"109";i:37;s:3:"110";i:38;s:3:"112";i:39;s:3:"127";i:40;s:3:"179";i:41;s:3:"180";i:42;s:3:"181";i:43;s:3:"278";i:44;s:3:"518";i:45;s:3:"519";i:46;s:3:"520";i:47;s:3:"521";i:48;s:3:"177";i:49;s:3:"178";i:50;s:3:"200";i:51;s:3:"231";i:52;s:3:"232";i:53;s:3:"233";i:54;s:3:"234";i:55;s:3:"522";i:56;s:3:"136";i:57;s:2:"12";i:58;s:3:"103";i:59;s:3:"175";i:60;s:3:"207";i:61;s:3:"137";i:62;s:1:"8";i:63;s:1:"9";i:64;s:3:"241";i:65;s:3:"242";i:66;s:3:"243";i:67;s:3:"244";i:68;s:3:"138";i:69;s:2:"71";i:70;s:2:"72";i:71;s:2:"75";i:72;s:2:"76";i:73;s:3:"245";i:74;s:3:"195";i:75;s:3:"196";i:76;s:3:"218";i:77;s:3:"230";i:78;s:3:"246";i:79;s:3:"525";i:80;s:3:"529";i:81;s:3:"133";i:82;s:3:"130";i:83;s:3:"167";i:84;s:3:"263";i:85;s:3:"264";i:86;s:3:"265";i:87;s:3:"266";i:88;s:3:"523";i:89;s:3:"226";i:90;s:3:"280";i:91;s:3:"281";i:92;s:3:"508";i:93;s:3:"509";i:94;s:3:"227";i:95;s:3:"267";i:96;s:3:"268";i:97;s:3:"247";i:98;s:3:"279";i:99;s:3:"139";i:100;s:3:"255";i:101;s:3:"261";i:102;s:3:"276";i:103;s:3:"155";i:104;s:3:"270";i:105;s:3:"271";i:106;s:3:"273";i:107;s:3:"274";i:108;s:3:"275";i:109;s:3:"285";i:110;s:3:"517";i:111;s:3:"524";i:112;s:3:"254";i:113;s:1:"5";i:114;s:1:"6";i:115;s:2:"11";i:116;s:2:"13";i:117;s:2:"14";i:118;s:2:"16";i:119;s:2:"20";i:120;s:2:"23";i:121;s:2:"52";i:122;s:2:"10";i:123;s:2:"45";i:124;s:2:"54";i:125;s:2:"67";i:126;s:2:"68";i:127;s:2:"69";i:128;s:2:"97";i:129;s:3:"117";i:130;s:3:"125";i:131;s:3:"126";i:132;s:2:"53";i:133;s:2:"70";i:134;s:3:"118";i:135;s:2:"82";i:136;s:2:"83";i:137;s:2:"87";i:138;s:3:"104";i:139;s:3:"121";i:140;s:3:"122";i:141;s:3:"123";i:142;s:3:"124";i:143;s:3:"152";i:144;s:3:"286";i:145;s:3:"287";i:146;s:3:"294";i:147;s:3:"295";i:148;s:3:"296";i:149;s:3:"302";i:150;s:3:"304";i:151;s:3:"305";i:152;s:3:"307";i:153;s:3:"308";i:154;s:3:"310";i:155;s:3:"311";i:156;s:3:"312";i:157;s:3:"313";i:158;s:3:"314";i:159;s:3:"315";i:160;s:3:"316";i:161;s:3:"317";i:162;s:3:"318";i:163;s:3:"319";i:164;s:3:"320";i:165;s:3:"321";i:166;s:3:"322";i:167;s:3:"323";i:168;s:3:"326";i:169;s:3:"327";i:170;s:3:"331";i:171;s:3:"332";i:172;s:3:"333";i:173;s:3:"334";i:174;s:3:"335";i:175;s:3:"340";i:176;s:3:"341";i:177;s:3:"342";i:178;s:3:"343";i:179;s:3:"344";i:180;s:3:"345";i:181;s:3:"346";i:182;s:3:"347";i:183;s:3:"348";i:184;s:3:"349";i:185;s:3:"361";i:186;s:3:"362";i:187;s:3:"363";i:188;s:3:"364";i:189;s:3:"369";i:190;s:3:"373";i:191;s:3:"374";i:192;s:3:"375";i:193;s:3:"376";i:194;s:3:"377";i:195;s:3:"378";i:196;s:3:"380";i:197;s:3:"382";i:198;s:3:"383";i:199;s:3:"384";i:200;s:3:"385";i:201;s:3:"386";i:202;s:3:"389";i:203;s:3:"390";i:204;s:3:"391";i:205;s:3:"392";i:206;s:3:"393";i:207;s:3:"394";i:208;s:3:"395";i:209;s:3:"396";i:210;s:3:"397";i:211;s:3:"398";i:212;s:3:"399";i:213;s:3:"400";i:214;s:3:"403";i:215;s:3:"404";i:216;s:3:"411";i:217;s:3:"412";i:218;s:3:"413";i:219;s:3:"414";i:220;s:3:"415";i:221;s:3:"416";i:222;s:3:"417";i:223;s:3:"419";i:224;s:3:"420";i:225;s:3:"421";i:226;s:3:"422";i:227;s:3:"423";i:228;s:3:"429";i:229;s:3:"434";i:230;s:3:"435";i:231;s:3:"436";i:232;s:3:"441";i:233;s:3:"442";i:234;s:3:"443";i:235;s:3:"445";i:236;s:3:"446";i:237;s:3:"448";i:238;s:3:"449";i:239;s:3:"451";i:240;s:3:"453";i:241;s:3:"455";i:242;s:3:"458";i:243;s:3:"459";i:244;s:3:"460";i:245;s:3:"461";i:246;s:3:"462";i:247;s:3:"463";i:248;s:3:"464";i:249;s:3:"465";i:250;s:3:"466";i:251;s:3:"467";i:252;s:3:"469";i:253;s:3:"470";i:254;s:3:"473";i:255;s:3:"480";i:256;s:3:"482";i:257;s:3:"483";i:258;s:3:"484";i:259;s:3:"485";i:260;s:3:"498";i:261;s:3:"507";i:262;s:3:"516";i:263;s:3:"527";i:264;s:3:"528";i:265;s:3:"530";i:266;s:3:"531";}';
    public $item = 'a:267:{i:0;s:22:"Addver/addadvertisings";i:1;s:21:"Addver/addadvertising";i:2;s:20:"CONSOLE_MEDIA_DELETE";i:3;s:20:"CONSOLE_MEDIA_UPLOAD";i:4;s:9:"Tpl/pulls";i:5;s:14:"play/MediaPlay";i:6;s:19:"Play/editorTemplate";i:7;s:17:"play/Advertisings";i:8;s:10:"Play/pulls";i:9;s:15:"Profile/Profile";i:10;s:24:"Addver/Maintenancerecord";i:11;s:17:"Customer/UserView";i:12;s:7:"MONITOR";i:13;s:27:"Monitoring/MonitoringInsert";i:14;s:17:"Download/Download";i:15;s:18:"play/deletesaddver";i:16;s:15:"CONSOLE_PROGRAM";i:17;s:15:"Setting/addlang";i:18;s:16:"Setting/editlang";i:19;s:15:"Examine/examine";i:20;s:13:"DEVICE_REROOT";i:21;s:16:"DEVICE_STOP_PLAY";i:22;s:17:"DEVICE_START_PLAY";i:23;s:23:"DEVICE_SET_POWER_ON_OFF";i:24;s:18:"DEVICE_ONLINE_TEST";i:25;s:17:"DEVICE_REDOWNLOAD";i:26;s:17:"DEVICE_SCREENSHOT";i:27;s:14:"DEVICE_RESTART";i:28;s:22:"play/MakeaddveraddGrou";i:29;s:15:"publish/derived";i:30;s:16:"Play/showaddvers";i:31;s:13:"publish/getAd";i:32;s:22:"Control/ControlPublish";i:33;s:14:"Control/Update";i:34;s:13:"DEVICE_DELETE";i:35;s:21:"Control/devicedeletes";i:36;s:23:"Control/ControlAddGroup";i:37;s:17:"introduction/View";i:38;s:22:"introduction/Solutions";i:39;s:24:"Monitoring/MonitoringDel";i:40;s:25:"introduction/Introduction";i:41;s:17:"Play/changeAdData";i:42;s:15:"template/temadd";i:43;s:13:"Data/Authcode";i:44;s:12:"DEVICE_SLEEP";i:45;s:21:"DEVICE_REMOVE_PROGRAM";i:46;s:16:"DEVICE_SET_VOICE";i:47;s:12:"DEVICE_RESET";i:48;s:8:"previews";i:49;s:14:"publish/Random";i:50;s:12:"Examine/view";i:51;s:12:"MediaExamine";i:52;s:12:"AdPubExamine";i:53;s:15:"FramePubExamine";i:54;s:25:"addver_v2/Tpl/pulls&tag=1";i:55;s:19:"addver_v2/Tpl/pulls";i:56;s:11:"DEVICE_BOOT";i:57;s:25:"CONSOLE_DEVICE_PERIPHERAL";i:58;s:16:"FINANCE_OVERVIEW";i:59;s:7:"CONSOLE";i:60;s:7:"FINANCE";i:61;s:14:"CONSOLE_DEVICE";i:62;s:16:"CONSOLE_TEMPLATE";i:63;s:13:"CONSOLE_MEDIA";i:64;s:15:"CONSOLE_CONTROL";i:65;s:7:"SETTING";i:66;s:14:"Role/Roleadmin";i:67;s:21:"CONSOLE_DEVICE_MANAGE";i:68;s:29:"CONSOLE_DEVICE_REMOTE_CONTROL";i:69;s:10:"USERCENTER";i:70;s:15:"Monitor/manager";i:71;s:13:"FINANCE_PRICE";i:72;s:20:"Setting/savetemplate";i:73;s:23:"CONSOLE_DEVICE_SCHEDULE";i:74;s:19:"CONSOLE_DEVICE_FILE";i:75;s:20:"DEVICE_SWITCH_SERVER";i:76;s:14:"DEVICE_UPGRADE";i:77;s:14:"DEVICE_REFRESH";i:78;s:11:"DEVICE_EDIT";i:79;s:11:"Intelligent";i:80;s:22:"Intelligent/intelltail";i:81;s:22:"CONSOLE_DEVICE_CONTROL";i:82;s:15:"template/option";i:83;s:15:"DEVICE_TRANSFER";i:84;s:17:"pub/publish/index";i:85;s:11:"FINANCE_BUY";i:86;s:14:"FINANCE_CHARGE";i:87;s:14:"PROGRAM_REVIEW";i:88;s:17:"CONSOLE_DASHBOARD";i:89;s:12:"DEVICE_GROUP";i:90;s:16:"DEVICE_GROUP_ADD";i:91;s:19:"DEVICE_RROUP_REMOVE";i:92;s:17:"DEVICE_GROUP_EDIT";i:93;s:10:"DEVICE_ADD";i:94;s:12:"PROGRAM_MAKE";i:95;s:13:"PROGRAM_GROUP";i:96;s:17:"PROGRAM_GROUP_ADD";i:97;s:20:"PROGRAM_GROUP_REMOVE";i:98;s:18:"PROGRAM_GROUP_EDIT";i:99;s:19:"CONSOLE_MEDIA_GROUP";i:100;s:15:"MEDIA_GROUP_ADD";i:101;s:18:"MEDIA_GROUP_REMOVE";i:102;s:16:"MEDIA_GROUP_EDIT";i:103;s:11:"CONTROL_ADD";i:104;s:15:"CONSOLE_PUBLISH";i:105;s:12:"FINANCE_BILL";i:106;s:6:"UNKONW";i:107;s:12:"SETTING_ROLE";i:108;s:24:"SETTING_ROLE_MANAGE_MENU";i:109;s:30:"FINANCE_PRICE_SET_OTHERS_PRICE";i:110;s:18:"FINANCE_PRICE_EDIT";i:111;s:27:"FINANCE_PRICE_SHOW_PURCHASE";i:112;s:23:"FINANCE_PRICE_SHOW_SALE";i:113;s:18:"FINANCE_WECHAT_PAY";i:114;s:14:"FINANCE_PAYPAL";i:115;s:25:"USERCENTER_ADD_ENTERPRISE";i:116;s:22:"USERCENTER_INFORMATION";i:117;s:26:"USERCENTER_EDIT_ENTERPRISE";i:118;s:20:"USERCENTER_ADD_STAFF";i:119;s:21:"USERCENTER_EDIT_STAFF";i:120;s:14:"SETTING_VERIFY";i:121;s:20:"DEVICE_BATCH_UPGRADE";i:122;s:14:"DEVICE_WAKE_UP";i:123;s:21:"FINANCE_EXCHANGE_RATE";i:124;s:21:"BUY_DEVICE_FOR_OTHERS";i:125;s:22:"BUY_STORAGE_FOR_OTHERS";i:126;s:31:"USERCENTER_EDIT_ROLE_FOR_OTHERS";i:127;s:13:"Common/upload";i:128;s:11:"Addver/edit";i:129;s:13:"Media/updates";i:130;s:13:"Media/deletes";i:131;s:19:"Media/UploadFileTwo";i:132;s:20:"Customer/addPersonal";i:133;s:22:"Customer/addEnterprise";i:134;s:16:"Customer/Entview";i:135;s:18:"Customer/EntDelete";i:136;s:20:"Customer/CustomerAdd";i:137;s:19:"Customer/UserUpdate";i:138;s:14:"Staff/StaffAdd";i:139;s:13:"Monitor/index";i:140;s:17:"Customer/Customer";i:141;s:11:"Staff/Staff";i:142;s:9:"Role/Role";i:143;s:15:"Staff/StaffView";i:144;s:17:"Staff/StaffUpdate";i:145;s:18:"Customer/EntUpdate";i:146;s:21:"Infocenter/Infocenter";i:147;s:17:"Staff/StaffDelete";i:148;s:12:"Role/RoleAdd";i:149;s:12:"Role/SetRole";i:150;s:15:"Role/RoleUpdate";i:151;s:13:"Media/addGrou";i:152;s:17:"Feedback/Feedback";i:153;s:14:"Feedback/index";i:154;s:17:"Language/Language";i:155;s:12:"Addver/index";i:156;s:11:"medea/media";i:157;s:9:"play/play";i:158;s:18:"Control/addControl";i:159;s:19:"Play/deletetemplate";i:160;s:19:"addver/devicereroot";i:161;s:17:"addver/devicestop";i:162;s:17:"addver/deviceplay";i:163;s:23:"addver/deviceScreenshot";i:164;s:22:"addver/devicekeepalive";i:165;s:23:"addver/deviceredownload";i:166;s:24:"addver/devicesleepplayer";i:167;s:20:"addver/devicerestart";i:168;s:20:"play/TemplateaddGrou";i:169;s:28:"Language/LanguageCacheUpdate";i:170;s:17:"Language/addlable";i:171;s:22:"Infocenter/viewcomment";i:172;s:23:"Language/langcontroller";i:173;s:20:"Setting/websitestyle";i:174;s:20:"Feedback/viewcomment";i:175;s:7:"ShareAd";i:176;s:13:"ShareTemplate";i:177;s:10:"ShareMedia";i:178;s:17:"Setting/webconfig";i:179;s:20:"Server/servermonitor";i:180;s:20:"addver/DasenUserInfo";i:181;s:29:"NavMessage/DasenBehaviordiary";i:182;s:16:"addver/DaSenDays";i:183;s:22:"addver/DaSenControlBtn";i:184;s:15:"help/newversion";i:185;s:26:"addver/devicesetpoweronoff";i:186;s:20:"Data/setdefaultlimit";i:187;s:15:"Device/transfer";i:188;s:21:"addver/deviceShutdown";i:189;s:23:"addver/devicerestartOne";i:190;s:18:"addver/devicevoice";i:191;s:22:"publish/InsertPlayList";i:192;s:34:"addver/devicesetpoweronoffeveryday";i:193;s:21:"addver/deviceOpendoor";i:194;s:18:"Financial/overview";i:195;s:32:"NavMessage/KangrongBehaviordiary";i:196;s:13:"preview/index";i:197;s:8:"map/bmap";i:198;s:18:"Setting/setWarning";i:199;s:17:"addver/deviceBoot";i:200;s:17:"device/peripheral";i:201;s:13:"profile/index";i:202;s:12:"topmain/main";i:203;s:14:"index/Overview";i:204;s:24:"/center/index.html#/data";i:205;s:18:"indexlefttop/index";i:206;s:17:"addver/showdevice";i:207;s:9:"play/show";i:208;s:11:"media/media";i:209;s:15:"Control/Control";i:210;s:16:"Infocenter/index";i:211;s:8:"Data/set";i:212;s:17:"NavMessage/Device";i:213;s:31:"NavMessage/DeviceBehaviorrecord";i:214;s:10:"user/index";i:215;s:13:"Role/MainMeum";i:216;s:12:"role/addmenu";i:217;s:16:"role/getMenuItem";i:218;s:11:"Examine/set";i:219;s:19:"Examine/delWorkflow";i:220;s:21:"Information/DialogTip";i:221;s:20:"Userinfo/getUserlist";i:222;s:24:"Setting/saveuploadconfig";i:223;s:25:"Setting/savepublishconfig";i:224;s:16:"Config/addconfig";i:225;s:15:"Setting/Setting";i:226;s:23:"Playrecord/PlaySchedule";i:227;s:23:"download/devicedownload";i:228;s:25:"addver/deviceswitchserver";i:229;s:19:"addver/deviceupdate";i:230;s:12:"device/Power";i:231;s:21:"NavMessage/Statistics";i:232;s:15:"publish/publish";i:233;s:23:"publish/getPageProgress";i:234;s:20:"download/getprogress";i:235;s:17:"Publish/flowspend";i:236;s:25:"publish/checkdataplaylist";i:237;s:15:"Examine/ischeck";i:238;s:19:"Control/loadplaypic";i:239;s:14:"addver/options";i:240;s:14:"addver/control";i:241;s:14:"addver/dasheng";i:242;s:14:"addver/kanrong";i:243;s:14:"schedule/index";i:244;s:8:"Platform";i:245;s:18:"FINANCE_BUY_DEVICE";i:246;s:19:"FINANCE_BUY_STORAGE";i:247;s:12:"PROGRAM_EDIT";i:248;s:14:"PROGRAM_DELETE";i:249;s:14:"PROGRAM_EXPORT";i:250;s:24:"CONSOLE_PROGRAM_SCHEDULE";i:251;s:24:"CONSOLE_PROGRAM_DOWNLOAD";i:252;s:5:"CHART";i:253;s:6:"NOTICE";i:254;s:17:"DEVICE_TAKE_PHOTO";i:255;s:20:"DEVICE_CAPTURE_VIDEO";i:256;s:21:"DEVICE_VOLUME_SETTING";i:257;s:25:"DEVICE_BRIGHTNESS_SETTING";i:258;s:7:"AI_PLAY";i:259;s:22:"FINANCE_BUY_PRICE_EDIT";i:260;s:25:"USERCENTER_ACCESS_RECORDS";i:261;s:16:"CONSOLE_OVERVIEW";i:262;s:25:"WIDGET_SYSTEM_INFORMATION";i:263;s:24:"WIDGET_DISTINGUISH_TOTAL";i:264;s:27:"WIDGET_EQUIPMENT_STATISTICS";i:265;s:34:"WIDGET_DISTINGUISH_TRAFFIC_RECORDS";i:266;s:19:"WIDGET_PEOPLE_TOTAL";}';
}
