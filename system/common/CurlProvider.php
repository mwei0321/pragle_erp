<?php

/**
 * curl 封装
 * @Author: MaWei
 * @Date:   2018-12-28
 * @Last Modified by:   MaWei
 * @Last Modified time: 2019-05-03
 */

namespace System\common;

class CurlProvider
{

    /**
     * cur post请求
     * [curlPost description]
     * @param  string $_url 请求地址
     * @param  array|string $_data 请求参数内容
     * @param  boolean $_isSyscn 是否同步返回
     * @return object
     * @Date   2018-12-28T13:38:58+0800
     * @Author MaWei
     * @Link   http://www.mawei.live
     */
    static function curlPost($_url, $_data, $_isSyscn = true)
    {
        if (!$_url || !$_data) {
            return false;
        }
        //初始化curl
        $ch = curl_init();
        //抓取指定网页
        curl_setopt($ch, CURLOPT_URL, $_url);
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, 0);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 跳过证书检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // 从证书中检查SSL加密算法是否存在
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, 1);
        //post内容
        curl_setopt($ch, CURLOPT_POSTFIELDS, $_data);
        //是否需要同步返回
        if (!$_isSyscn) {
            // curl_setopt($ch, CURLOPT_TIMEOUT, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, 10);
        }
        //运行curl，并返回结果
        $result = curl_exec($ch);
        //关闭
        curl_close($ch);
        //返回结果
        return $result;
    }

    /**
     * curl get请求
     * @param  string $_url 请求地址
     * @param  boolean $_isSyscn 是否同步返回
     * @return [type] [description]
     * @Date   2018-12-28T13:03:48+0800
     * @Author MaWei
     * @Link   http://www.mawei.live
     */
    static function curlGet($_url, $_isSyscn = true)
    {
        if (!$_url) {
            return false;
        }
        //初始化curl
        $ch = curl_init();
        //抓取指定网页
        curl_setopt($ch, CURLOPT_URL, $_url);
        //设置头文件的信息作为数据流输出
        curl_setopt($ch, CURLOPT_HEADER, 0);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 跳过证书检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // 从证书中检查SSL加密算法是否存在
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        //是否需要同步返回
        if (!$_isSyscn) {
            // curl_setopt($ch, CURLOPT_TIMEOUT, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, 30);
        }
        //运行curl，并返回结果
        $result = curl_exec($ch);
        //关闭
        curl_close($ch);
        //返回结果
        return $result;
    }
}
