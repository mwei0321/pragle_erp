<?php

    /**
     * 服务地图
     * @Author: MaWei
     * @Date:   2021-10-27
     * @Last Modified by:   mawei
     * @Last Modified time: 2021-12-23
     */
    namespace system\common;

    class SrvMap {
        // 数据基础
        public $BaseDB = 'system\common\BaseDB';

        // service
        public $Kpi = 'system\services\kpi\KpiServices';


        // 引用旧的代码

        /**
         * 返回定义的成员属性列表
         * @return [type] [description]
         * @Date   2019-03-13T17:56:51+0800
         * @Author MaWei <1123265518@qq.com>
         * @Link   http://mawei.live
         */
        function toArray() {
            $ref = new \ReflectionClass(static::class);
            $propArr = $ref->getProperties ();
            $attrArr = [];
            foreach ( $propArr as $obj ) {
                $name = $obj->getName();
                @$this->$name && $attrArr[$name] = $this->$name;
            }
            return $attrArr;
        }
    }