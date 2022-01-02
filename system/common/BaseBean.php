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
    public $page      = 1;
    public $count     = 0;
    public $offset    = 0;
    public $limit     = 0;
    public $pageSize  = 10;
    public $totalPage = 0;
    public $pageRaw   = [];
    // 错误
    public $errCode   = 0;
    public $errMsg    = '';

    /**
     * 分页处理
     * @return [type] [description]
     * @Date   2021-12-23T10:46:45+0800
     * @Author MaWei <1123265518@qq.com>
     * @Link   http://mawei.live
     */
    function page()
    {
        // 页码过滤
        if ($this->page < 1) {
            $this->page = 1;
        }
        // 总页数
        $this->totalPage = ceil((intval($this->count) / $this->pageSize));
        // 分页偏移量
        $this->offset    = ($this->page - 1) * $this->pageSize;
        $this->limit     = $this->pageSize;
        // 分布信息
        $this->pageRaw = [
            'count'     => $this->count,
            'totalPage' => $this->totalPage,
            'offset'    => $this->offset,
            'limit'     => $this->limit,
            'page'      => $this->page,
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
    function toArray()
    {
        $ref = new \ReflectionClass(get_called_class());
        $propArr = $ref->getProperties();
        $attrArr = [];
        foreach ($propArr as $obj) {
            $name = $obj->getName();
            $value = $this->$name ?? '';
            $attrArr[$name] = is_string($value) ? $this->_text($value) : $value;
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
