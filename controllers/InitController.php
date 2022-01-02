<?php

    /**
     * 控制器初始化入口
     * @Author: mawei
     * @Date:   2021-12-22
     * @Last Modified by:   mawei
     * @Last Modified time: 2021-12-23
     */
    
    namespace app\controllers;

    use Yii;
    use yii\web\Controller;
    use system\common\HelperFuns;

    class InitController extends Controller {
        // 登录用户ID
        public $userId = 1;
        public $enterpriseId = 1;
        public $page = [];

        function init () {
            parent::init();
            $this->checkLogin();
        }

        /* 返回json数据
         * @param  array $_data 数据
         * @param  int $_code 返回代码
         * @param  string $_msg 返回提示消息
         * @param  array $_aa 扩展数据
         * @return json array
         * @Date   2020-03-31
         * @Author MaWei
         * @Link   http://www.mawei.live
         */
        function reJson($_data = [],$_msg="return success!",$_code = 200) {
            $data = [
                'code' => $_code,
                'msg'  => $_msg,
                'data' => $_data,
                'page' => $this->page,
            ];

            // 给 header 头信息添加 token 验证信息
            $tokenData = [
                'user_id' => $this->userId,
                'enterprise_id' => $this->enterpriseId,
                'time' => time(),
            ];
            header('Authorization',HelperFuns::revEncrypt(json_encode($tokenData)));
            header('content-type:application/json');

            echo json_encode($data);
            exit;
        }

        /**
         * 登录检查
         * @return [type] [description]
         * @Date   2021-12-22T14:37:34+0800
         * @Author MaWei <1123265518@qq.com>
         * @Link   http://mawei.live
         */
        private function checkLogin () {
            $token = 'UlYza3dLMjNxRnkwZVZtcGxhcWFvQXhlTUhva2NOdlF0Z1RFNFNVTEkzM2tUQUx3djA5ZmVBd2xVWGora1g1Njo6wYu+SxdOH6aPkD8MkP55vw==';
            //如果是nginx
            if(isset($_SERVER['HTTP_AUTHORIZATION'])){
                $token = $_SERVER['HTTP_AUTHORIZATION'];
            }elseif(function_exists('getallheaders')){
                $headers = getallheaders();
                if(isset($headers['Authorization']) || isset($headers['authorization'])){
                    $token = $headers['Authorization'] ?? $headers['authorization'];
                }
            }
            //过滤路由
            // if(in_array(Yii::$app->requestedRoute,$this->_filterRouters)){
            //     $this->adminId = 1;
            //     return true;
            // }
            //只能测试用
            if(in_array($_SERVER['HTTP_HOST'],['api.pe.com','pe.com']) || isset($_REQUEST['debug']) && $_REQUEST['debug'] == 'mw'){
                $this->enterpriseId = 1;
                $this->userId       = 1;
                return true;
            }
            //验证TOKEN
            if($token){
                $token = HelperFuns::revDecrypt($token);
                if($token){
                    $tokenData = json_decode($token);
                    $time = intval($tokenData->ctime??0);
                    $this->userId = $tokenData->user_id;
                    if(($time + 7200) > time() && $this->userId > 0){
                        return true;
                    }
                }
            }

            return $this->reJson($token,'登录失效!',4001);
        }

    }
