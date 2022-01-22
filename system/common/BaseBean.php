<?php

/**
 * @Author: MaWei
 * @Date:   2021-10-27
 * @Last Modified by:   mawei
 * @Last Modified time: 2021-12-23
 */

namespace system\common;

use system\common\BeanInterface;

class BaseBean implements BeanInterface
{
    // 分页
    public $page       = 1;
    public $count      = 0;
    public $offset     = 0;
    public $limit      = 0;
    public $page_size  = 10;
    public $total_page = 0;
    public $pageRaw    = [];
    // 错误
    public $errCode    = 0;
    public $errMsg     = '';

    /**
     * 分页处理
     * @return [type] [description]
     * @Date   2021-12-23T10:46:45+0800
     * @Author MaWei <1123265518@qq.com>
     * @Link   http://mawei.live
     */
    function page($_count = null)
    {
        // 总记录数
        $_count && $this->count = $_count;

        // 页码过滤
        if ($this->page < 1) {
            $this->page = 1;
        }
        // 总页数
        $this->total_page = ceil((intval($this->count) / $this->page_size));
        // 分页偏移量
        $this->offset    = ($this->page - 1) * $this->page_size;
        $this->limit     = $this->page_size;
        // 分布信息
        $this->pageRaw = [
            'count'      => $this->count,
            'total_page' => $this->total_page,
            'offset'     => $this->offset,
            'limit'      => $this->limit,
            'page'       => $this->page
        ];

        return $this->pageRaw;
    }

    /**
     * 返回定义的成员属性列表
     * @return [type] [description]
     * @Date   2019-03-13T17:56:51+0800
     * @Author MaWei <1123265518@qq.com>
     * @Link   http://mawei.live
     */
    function toArray($_this = true)
    {
        $className = get_called_class();
        $ref = new \ReflectionClass($className);
        $propArr = $ref->getProperties();
        $attrArr = [];
        if ($_this) {
            foreach ($propArr as $obj) {
                if ($obj->class == $className) {
                    $name = $obj->name;
                    $value = $this->$name ?? '';
                    $attrArr[$name] = is_string($value) ? $this->_text($value) : $value;
                }
            }
        } else {
            foreach ($propArr as $obj) {
                $name = $obj->getName();
                $value = $this->$name ?? '';
                $attrArr[$name] = is_string($value) ? $this->_text($value) : $value;
            }
        }
        return $attrArr;
    }

    /**
     * 附值
     * @param  array $_vals [description]
     * @Date   2021-10-27T17:14:10+0800
     * @Author MaWei <1123265518@qq.com>
     * @Link   http://mawei.live
     */
    function setVals(array $_vals)
    {
        $attrArr = $this->toArray();

        if ($attrArr && $_vals) {
            foreach ($attrArr as $k => $v) {
                if (isset($_vals[$k])) {
                    $this->$k = $_vals[$k];
                }
            }
        }

        return true;
    }

    /**
     * 字符串过滤
     * @param  string $text [description]
     * @return [type] [description]
     * @Date   2021-10-28T14:40:55+0800
     * @Author MaWei <1123265518@qq.com>
     * @Link   http://mawei.live
     */
    protected function _text($text)
    {
        // $text = addslashes($text);
        $text = trim($text);
        return $text;
    }
}
