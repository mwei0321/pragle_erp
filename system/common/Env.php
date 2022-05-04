<?php
/*
* env配置
* @Author: MaWei
* @Date: 2022-05-02 10:11:45
* @Last Modified by: MaWeii
* @Last Modified time: 2022-05-02 10:12:076
*/

namespace system\common;

class Env
{
    const ENV_PREFIX = '';

    /**
     * 加载配置文件
     * @access public
     * @param string $filePath 配置文件路径
     * @return void
     */
    static function loadFile(string $_filePath)
    {
        if (!file_exists($_filePath)) throw new \Exception('配置文件' . $_filePath . '不存在');
        //返回二位数组
        $env = parse_ini_file($_filePath);
        foreach ($env as $key => $val) {
            $prefix = static::ENV_PREFIX . strtoupper($key);
            if (is_array($val)) {
                foreach ($val as $k => $v) {
                    $item = $prefix . '_' . strtoupper($k);
                    putenv("$item=$v");
                }
            } else {
                putenv("$prefix=$val");
            }
        }
    }

    /**
     * 获取环境变量值
     * @access public
     * @param string $_name 环境变量名（支持二级 . 号分割）
     * @param string $default 默认值
     * @return mixed
     */
    public static function get(string $_name, $default = null)
    {
        $result = getenv(static::ENV_PREFIX . strtoupper(str_replace('.', '_', $_name)));

        if (false !== $result) {
            if ('false' === $result) {
                $result = false;
            } elseif ('true' === $result) {
                $result = true;
            }
            return $result;
        }
        return $default;
    }
}
