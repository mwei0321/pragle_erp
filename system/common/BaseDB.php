<?php

/**
 * 数据库操作
 * @Author: MaWei
 * @Date:   2021-10-26
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-04-06 14:09:22
 */

namespace system\common;

use yii\db\Query;

class BaseDB
{

    /**
     * 根据ID返回详情
     * @param  string $_tableName 表名
     * @param  int $_id ID
     * @param  string $_field 字段
     * @return array [description]
     * @Date   2019-03-26T19:11:48+0800
     * @Author MaWei
     * @Link   http://www.mawei.live
     */
    function getInfoById(int $_id, $_field = '*', $_tableName = null)
    {
        $tableName = $_tableName ? $_tableName : $this->tableName;
        return (new Query())->from($tableName)
            ->select($_field)
            ->where(['id' => $_id])
            ->one();
    }

    /**
     * 根据条件返回详情
     * @param  string $_tableName 表名
     * @param  array $_where 条件
     * @param  string $_field 字段
     * @return [type] [description]
     * @Date   2019-04-26T11:23:50+0800
     * @Author MaWei
     * @Link   http://www.mawei.live
     */
    function getInfoByCondition($_where, $_field = '*', $_tableName = null, $_orderBy = "id DESC")
    {
        $tableName = $_tableName ? $_tableName : $this->tableName;
        $query = (new Query())->from($tableName)
            ->select($_field)
            ->limit(1)
            ->where($_where);
        return $_orderBy ? $query->orderBy($_orderBy)->one() : $query->one();
    }

    /**
     * 提取列表
     * @param  string $_tableName 表名
     * @param  string $_where 条件
     * @param  string $_field 字段
     * @param  string $_orderBy 排序
     * @return [type] [description]
     * @Date   2019-03-29T18:50:36+0800
     * @Author MaWei
     * @Link   http://www.mawei.live
     */
    function getListByCondition($_where = '', $_field = '*', $_tableName = null, $_orderBy = 'id DESC', $_fieldToKey = null, $_groupBy = null)
    {
        $tableName = $_tableName ? $_tableName : $this->tableName;
        $query = (new Query())->from($tableName)
            ->select($_field)
            ->where($_where)
            ->orderBy($_orderBy);
        if ($_fieldToKey) {
            $query->indexBy($_fieldToKey);
        }
        return $query->all();
    }

    /**
     * 返回某个字段的值
     * @param  string $_tableName 表名
     * @param  array $_where 条件
     * @param  string $_field 字段
     * @return string
     * @Date   2019-05-14T09:57:10+0800
     * @Author MaWei
     * @Link   http://www.mawei.live
     */
    function getFieldValByCondition($_where, $_field = 'id', $_tableName = null, $_orderBy = 'id DESC')
    {
        $tableName = $_tableName ? $_tableName : $this->tableName;
        $result = (new Query())->from($tableName)
            ->select($_field)
            ->where($_where)
            ->orderBy($_orderBy)
            ->limit(1)
            ->one();
        return $result[$_field] ?? '';
    }

    /**
     * 返回某个字段的值
     * @param  string $_tableName 表名
     * @param  array $_where 条件
     * @param  string $_field 字段
     * @return string
     * @Date   2019-05-14T09:57:10+0800
     * @Author MaWei
     * @Link   http://www.mawei.live
     */
    function getFieldValById($_id, $_field = 'id', $_tableName = null, $_orderBy = 'id DESC')
    {
        $tableName = $_tableName ? $_tableName : $this->tableName;
        $result = (new Query())->from($tableName)
            ->select($_field)
            ->where(['id' => $_id])
            ->orderBy($_orderBy)
            ->one();
        return $result[$_field] ?? '';
    }

    /**
     * 数据库更新
     * @param  string $_tableName 表名
     * @param  array $_data 更新数据
     * @param  string|array $_condition 条件
     * @return boolean
     * @Date   2019-03-26T19:15:33+0800
     * @Author MaWei
     * @Link   http://www.mawei.live
     */
    function update($_condition, $_data, $_tableName = null)
    {
        $tableName = $_tableName ? $_tableName : $this->tableName;
        return \Yii::$app->db->createCommand()
            ->update($tableName, $_data, $_condition)
            ->execute();
    }

    /**
     * 更新数据
     * @param  array $_data 更新的数据
     * @param  int $_id ID
     * @param  string $_tableName 表名
     * @return [type] [description]
     * @Date   2020-08-24T11:34:19+0800
     * @Author MaWei
     * @Link   http://www.mawei.live
     */
    function updateById($_id, $_data, $_tableName = null)
    {
        $tableName = $_tableName ? $_tableName : $this->tableName;
        return \Yii::$app->db->createCommand()
            ->update($tableName, $_data, ['id' => $_id])
            ->execute();
    }

    /**
     * 基于条件更新数据
     * @param  array $_data 更新的数据
     * @param  string $_condition 条件
     * @param  string $_tableName 表名
     * @return [type] [description]
     * @Date   2019-03-26T19:15:33+0800
     * @Author MaWei
     * @Link   http://www.mawei.live
     */
    function updateByCondition($_condition, $_data,  $_tableName = null)
    {
        $tableName = $_tableName ? $_tableName : $this->tableName;
        return \Yii::$app->db->createCommand()
            ->update($tableName, $_data, $_condition)
            ->execute();
    }


    /**
     * 返回总数
     * @param  string $_tableName 表名
     * @param  array $_where 条件
     * @return [type] [description]
     * @Date   2019-07-16T16:50:19+0800
     * @Author MaWei
     * @Link   http://www.mawei.live
     */
    function getCount($_where, $_tableName = null)
    {
        $tableName = $_tableName ? $_tableName : $this->tableName;
        return (new Query())->from($tableName)
            ->where($_where)
            ->count();
    }

    /**
     * 判断是否丰硕
     * @param  [type]  $_where
     * @param  [type]  $_tableName
     * date: 2022-01-12 10:21:40
     * @author  <mawei.live>
     * @return boolean
     */
    function isExsit($_where, $_tableName = null)
    {
        $tableName = $_tableName ? $_tableName : $this->tableName;
        $cnt = (new Query())->from($tableName)
            ->where($_where)
            ->count();
        return intval($cnt) > 0 ? true : false;
    }

    /**
     * 执行sql
     * @param  string $_tableName 表名
     * @param  string $_sql sql
     * @param  boolean $_isMulti 是否多条
     * @return [type] [description]
     * @Date   2019-07-16T16:07:57+0800
     * @Author MaWei
     * @Link   http://www.mawei.live
     */
    function query($_sql, $_isMulti = true)
    {
        if ($_isMulti) {
            return \Yii::$app->db->createCommand($_sql)->queryAll();
        } else {
            return \Yii::$app->db->createCommand($_sql)->queryOne();
        }
    }

    /**
     * 字段自增
     * @return [type] [description]
     * @Date   2019-06-25T14:48:51+0800
     * @Author MaWei
     * @Link   http://www.mawei.live
     */
    function increment($_field, $_condition, $_tableName = null, $_step = 1, $_set = null)
    {
        $tableName = $_tableName ? $_tableName : $this->tableName;
        $sql = "UPDATE `{$tableName}` SET `{$_field}`=`{$_field}` + {$_step} " . ($_set ? ",{$_set}" : '') . " WHERE {$_condition}";
        return \Yii::$app->db->createCommand($sql)->execute();
    }

    /**
     * 字段自减
     * @return [type] [description]
     * @Date   2019-06-25T14:48:51+0800
     * @Author MaWei
     * @Link   http://www.mawei.live
     */
    function decrement($_field, $_condition, $_tableName = null, $_step = 1)
    {
        $tableName = $_tableName ? $_tableName : $this->tableName;
        $sql = "UPDATE `{$tableName}` SET `{$_field}`=`{$_field}` - {$_step} WHERE {$_condition}";
        return \Yii::$app->db->createCommand($sql)->execute();
    }

    /**
     * 数据库写入
     * @param  string $_tableName 表名
     * @param  array $_data 写入数据
     * @return boolean
     * @Date   2019-03-26T19:16:49+0800
     * @Author MaWei
     * @Link   http://www.mawei.live
     */
    function insert($_data, $_tableName = null)
    {
        $tableName = $_tableName ? $_tableName : $this->tableName;
        $result = \Yii::$app->db->createCommand()
            ->insert($tableName, $_data)
            ->execute();
        if ($result === false) {
            return 0;
        }
        //返回ID
        return \Yii::$app->db->getLastInsertID();
    }

    /**
     * 删除
     * @param  string $_tableName 表名
     * @param  int $_id ID
     * @param  array $_data 更新数据
     * @param  boolean $_isRealDelete 是否直删除
     * @return [type] [description]
     * @Date   2019-06-06T14:26:11+0800
     * @Author MaWei
     * @Link   http://www.mawei.live
     */
    function delById($_id, $_data = [], $_tableName = null, $_isRealDelete = false)
    {
        $tableName = $_tableName ? $_tableName : $this->tableName;
        if ($_isRealDelete) {
            return \Yii::$app->db->createCommand()->delete($tableName, ['id' => $_id])->execute();
        } else {
            return \Yii::$app->db->createCommand()->update($tableName, $_data, ['id' => $_id])->execute();
        }
    }

    /**
     * 删除记录
     * @param  string $_tableName 表名
     * @param  array|string $_condition 条件
     * @return [type] [description]
     * @Date   2019-05-11T11:50:55+0800
     * @Author MaWei
     * @Link   http://www.mawei.live
     */
    function delete($_condition, $_data, $_tableName = null, $_isRealDelete = false)
    {
        $tableName = $_tableName ? $_tableName : $this->tableName;
        if ($_isRealDelete) {
            return \Yii::$app->db->createCommand()->delete($tableName, $_condition)->execute();
        } else {
            return \Yii::$app->db->createCommand()->update($tableName, $_condition, $_data)->execute();
        }
    }

    /**
     * [__construct description]
     * @param  [type] $_tableName [description]
     * @Date   2020-05-29T17:42:22+0800
     * @Author MaWei
     * @Link   http://www.mawei.live
     */
    function __construct($_tableName = null)
    {
        $_tableName && $this->tableName = $_tableName;
    }

    public $tableName = null;
}
