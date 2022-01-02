<?php
    /**
     * 逻辑服务工厂
     * @Author: MaWei
     * @Date:   2021-10-27
     * @Last Modified by:   mawei
     * @Last Modified time: 2021-12-23
     */
    namespace system\common;
    use system\common\SrvMap;

    class ServiceFactory {

        /**
         * 创建实例
         * [getInstance description]
         * @return [type] [description]
         * @Date   2019-03-13T14:03:56+0800
         * @Author MaWei
         * @Link   http://www.mawei.live
         */
        public static function getInstance($_SrvName,$_tableName = null){
            //初始化逻辑类
            if(static::$_SrvImps === null){
                //初始化注册类
                static::init();
            }
            //读取缓存
            if(isset(static::$_SrvImpsArray[$_SrvName])){
                return static::$_SrvImpsArray[$_SrvName];
            }
            //创建指定业务对象
            $SrvObj = static::createInstance($_SrvName,$_tableName);

            //缓存服务,如果是基础数据库操作类不缓存
            ($_SrvName !== 'BaseDB') && static::$_SrvImpsArray[$_SrvName] = $SrvObj;

            //返回实例
            return $SrvObj;
        }

        /**
         * 创建实例对象
         * [createInstance description]
         * @param  [type] $_SrvName [description]
         * @return [type] [description]
         * @Date   2019-03-13T15:54:23+0800
         * @Author MaWei
         * @Link   http://www.mawei.live
         */
        private static function createInstance($_SrvName,$_tableName = null){
            if(!isset(static::$_SrvImps[$_SrvName])){
                throw new \Exception("Services Class address not exsit");
            }
            //自动创建对象实例
            $classAddr = static::$_SrvImps[$_SrvName];
            return new $classAddr($_tableName);
        }

        /**
         * 初始化逻辑类
         * [init description]
         * @return [type] [description]
         * @Date   2019-03-13T15:29:52+0800
         * @Author MaWei
         * @Link   http://www.mawei.live
         */
        private static function init(){
            //注册类
            static::$_SrvImps = (new SrvMap())->toArray();
        }

        /**
         * 防止克隆类
         * [clone description]
         * @return [type] [description]
         * @Date   2019-03-13T14:04:58+0800
         * @Author MaWei
         * @Link   http://www.mawei.live
         */
        private function clone(){

        }

        /**
         * 单例私有化
         * [__construct description]
         * @Date   2019-03-13T14:03:13+0800
         * @Author MaWei
         * @Link   http://www.mawei.live
         */
        function __construct(){

        }

        //对象实例
        private static $_SrvImps = null;
        //对象实例缓存
        private static $_SrvImpsArray = [];
    }
