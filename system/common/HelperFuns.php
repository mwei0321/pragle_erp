<?php

/**
 * 常用公共方法
 * @Author: mawei
 * @Date:   2021-12-22
 * @Last Modified by:   mawei
 * @Last Modified time: 2021-12-23
 */
namespace system\common;

class HelperFuns{

    /**
     * 把数组的某的值做为键
     * @param array $_data 数组
     * @param string $_field key
     * @return array $newdata
     * @author MaWei ( http://www.mawei.live )
     * @date 2014-4-17 下午1:50:15
     */
    static function fieldtokey($_data,$_field = 'id'){
        $newdata = array();
        foreach ($_data as $k => $v){
            if(is_array($v["$_field"])){
                foreach ($v["$_field"] as $key => $val){
                    $newdata[$v["$_field"]][$val["$_field"]] = $val;
                }
            }
            $newdata[$v["$_field"]] = $v;
        }
        return $newdata;
    }

    /**
     * 把某个想同的键值合并成一个数据
     * @param  array $_data 数据
     * @param  string $_field 下标
     * @return [type] [description]
     * @Date   2019-12-31T14:00:00+0800
     * @Author MaWei
     * @Link   http://www.mawei.live
     */
    static function classifyMergeArray($_data,$_field = 'id'){
        if ( empty($_data) || !is_array($_data) ) return $_data;

        $newdata = [];
        foreach ($_data as $k => $v) {
            $newdata[$v["$_field"]][] = $v;
        }
        return $newdata;
    }


    /**
     * 文件上传
     * @param  $_param[
     *              'upKey', //接收KEY
     *              'upPath', //存放路径
     *              'name', //文件名
     *              'isDate', //是否按月份存放
     *              'filterExe', //过滤的扩展名
     *              'maxSize',//最大文件大小,单位字节，默认20M
     *         ];
     * @return string|int $info (-1:文件类型错误,-2:文件太大)
     * @author MaWei (http://www.mawei.live)
     * @date 2014-10-19  下午2:55:23
     */
    static function fileUpload($_param = []) {
        $file = isset($_param['upKey']) ? $_param['upkey'] : $_FILES['file'];
        $absPath = ROOT_PATH.$_param['upPath'].'/';
        $relPath = $_param['upPath'].'/';
        //日期路径处理
        if(isset($_param['isDate'])) {
            $relPath .= date('Ym',time()).'/';
            $absPath .= date('Ym',time()).'/';
        }
        //文件名生成
        $filename = isset($_param['name']) ? $file['name'] : date('YmdHis').rand(3000,99999);
        //过滤文件类型
        $exename = ['gif','jpg','jpeg','bmp','png','swf','txt','xls','doc','xlsx','docx','zip','rar','7z'];
        $fileexename = self::getFileExeName($file['name']);
        //添加文件扩展名
        $filename .= '.'.$fileexename;
        if(!in_array($fileexename,isset($_param['filterExe']) ? $_param['filterExe'] : $exename)){
            return -1;//文件类型错误
        }
        //过滤文件大小
        if($file['size'] > (isset($_param['maxSize']) ? $_param['maxSize'] : 24657920) || $file['size'] == -1){
            return -2;//文件太大
        }
        //创建目录
        $result = self::createDir($absPath);
        $fullPath = self::autoCharset($absPath.$filename,'utf-8','gbk');
        try {
            move_uploaded_file($file['tmp_name'], $fullPath);
        } catch (\Exception $e) {
            throw new \Exception($e, 1);
        }
        return trim($relPath.$filename);
    }

    /**
     * 下载文件
     * @param  string $_url 下载文件的地址
     * @param  string $_path 存放路径（默认为 UPLOAD_PATH 定义下
     * @param  string $_name 重命名
     * @param  string $_chmod 权限
     * @return array
     * @author MaWei (http://www.mawei.live)
     * @date 2014-8-3  下午2:10:22
     * @qq群号：341411327
     */
    static function downFile($_url,$_path = null,$_name = null,$_chmod = '0444'){
        ob_start();
        readfile($_url);
        $img = ob_get_contents();
        ob_end_clean();
        $exname = getFileExeName($_url);
        if(! $_name){
            $_name = date('YmdHms').randString(5).'.'.$exname;
        }else{
            $_name = $_name.'.'.$exname;
        }
        //默认路径
        if(! $_path){
            $_path = UPLOAD_PATH.'avatar/'.date('Ym').'/';
        }

        createDir($_path);
        $path = $_path.$_name;
        file_put_contents($path, $img);
        if(is_file($path)){
            chmod($path, $_chmod);//这步不能少，防病毒攻击
            return $path;
        }
        return null;
    }

    /**
     * 图片水印文字
     * [createImg description]
     * @param  [type] $_info [description]
     * @return [type] [description]
     * @Date   2018-11-05T13:54:55+0800
     * @Author MaWei
     * @Link   http://www.mawei.live
     */
    static function diploma($_imgpath,$_name){
        $exename = self::getFileExeName($_imgpath);
        switch ($exename) {
            case 'jpg':
            case 'jpeg':
                $bg = imagecreatefromjpeg($_imgpath);
                break;
            case 'png':
                $bg = imagecreatefrompng($_imgpath);
                break;
            default:
                return null;
                break;
        }
        //路径
        $path = '/uploads/diploma/';
        //载入字体
        $font = ROOT_PATH.'/font/bold.otf';
        //创建颜色
        $fontColor = imagecolorallocate($bg,44,17,5);
        //创建文件夹
        $path .= date('Y',time()).'/';
        self::createDir(ROOT_PATH.$path);
        //字数
        $num = mb_strlen($_name);
        if($num>4){
            $num = 4;
            $_name = mb_substr($_name, 0,4);
        }
        //写入文字 (图片,大小磅,角度,坐标x,坐标y,字体颜色,字体,文字)
        imagefttext($bg,30,0,(150+(4-$num)*28),555,$fontColor,$font,$_name);


        // $avatar = imagecreatefromjpeg($_info['avatar']);
        // imagecopyresized($bg,$avatar,8,114,0,0,720,405,imagesx($avatar),imagesy($avatar));

        // $QRCdoe = imagecreatefrompng(ROOT_PATH.'/Uploads/redflag/QRCode.png');
        // $redflagphoto = imagecopymerge($bg,$QRCdoe,85,809,0,0,imagesx($QRCdoe),imagesy($QRCdoe),100);

        $filename = $path.date('YmdHis').rand(300,999999).'.png';
        imagepng($bg,ROOT_PATH.$filename);
        imagedestroy($bg);
        return $filename;
    }

    /**
     * 分页处理
     * @param  integer $_count 总数
     * @param  integer $_page 页数
     * @param  integer $_pageSize 页面记录条数
     * @return [type] [description]
     * @Date   2018-07-27T10:41:09+0800
     * @Author MaWei
     * @Link   http://www.mawei.live
     */
    static function page($_count,$_page = 1,$_pageSize = 20){
        $totalPage = ceil(($_count/$_pageSize));
        $offset = ($_page - 1) * $_pageSize;
        $limit = $_pageSize;
        $pages = [
            'totalPage' => $totalPage,
            'offset'    => $offset,
            'limit'     => $limit,
            'page'      => $_page,
            'isEnd'     => ($offset > $totalPage) ? 1 : 0,
        ];
        return $pages;
    }

    /**
     * 分页处理
     * @param    {[type]} $page [description]
     * @param    {[type]} $totalpage [description]
     * @return   {[type]} [description]
     * @dateTime 2018-04-27
     * @author MaWei<www.mawei.live>
     */
    static function pagehtml ($_totalNum,$_pageSize = 50,$_url = null,$_ajax = false){
        //当前页码
        $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        //生成url
        $url = $_url ? $_url : $_SERVER['REQUEST_URI'];
        $url .= (strpos($url, '?') === false) ? '?page=' : '&page=';
        //拼装url
        $realUrl = $_ajax ? 'javascript:;" onclick="ajaxpage('.$page.');' : $url ;
        // var_dump($realUrl);exit;
        //总页数
        $totalpage = ceil(($_totalNum/$_pageSize));
        //中间步进数
        $pagenp = 2;
        //显示多少个页码
        $pageshownum = 5;
        //拼装html
        $html = '<ul class="pagination"> ';
        //如果页码大于1,生成html
        if($totalpage > 1){
            $html .= '';
            //第一页处理.第一页面总显示
            if($page < 2){//如果当前为第一页时
                $html .= '<li class="paginate_button previous disabled"><a href="javascript:;">&lt;&lt;</a></li>';
            }else{
                $html .= '<li class="paginate_button previous"><a href="'.$realUrl.'1">&lt;&lt;</a></li>';
            }
            //判断中间页码显示
            $i = 1;
            $j = $totalpage;
            if($totalpage > $pageshownum){//如果总页数大于总显示页码数,中间页码处理
                if(($page >= $pageshownum)){//如果当前页码大于总显示页码数,
                    if($page <= ($totalpage - $pageshownum)){ //
                        $i = $page - intval($pageshownum/2);
                        $j = $i+$pageshownum;
                    }else{
                        $i = $totalpage - $pageshownum;
                    }
                }else{
                    $j = $pageshownum;//
                }
            }
            //处理中间页码
            for (; $i <= $j; $i++) {
                if($i == $page){ //当前页码url处理
                    $html .= '<li class="paginate_button disabled"><a href="javascript:;">'.$i.'</a></li>';
                }else{
                    $html .= '<li class="paginate_button"><a href="'.$realUrl.$i.'">'.$i.'</a></li>';
                }
            }
            //最后一页处理
            if($page >= $totalpage){//当前页码到最后一页url处理
                $html .= '<li class="paginate_button next disabled"><a href="javascript:;">&gt;&gt;</a></li>';
            }else{
                $html .= '<li class="paginate_button next"><a href="'.$realUrl.$totalpage.'">&gt;&gt;</a></li>';
            }
        }
        $html .= '</ul>';
        return $html;
    }

    /**
     * 生成基于时间指定的长度的字符串
     * @param    string $_prefix 字符串前缀
     * @param    integer $_length 最小14位
     * @return   string 字符串
     * @Author   MaWei
     * @Link     http://www.mawei.live
     * @DateTime 2018-07-18
     */
    static function getStrByTime($_prefix = '',$_length = 20){
        $time = date('YmdHis');
        $time2 = microtime();
        if($_length < 14){
            return $time;
        }elseif ($_length >= 14) {
            $str = $_prefix.$time;
            $length = strlen($str);
            $diffleng = $_length - $length;
            if($diffleng > 0 ){
                $randStr = self::randString($diffleng,1);
                $str = $_prefix.$time.$randStr;
            }
        }
        return $str;
    }

    /**
     * 生成订单流水
     * [getOrderSN description]
     * @return [type] [description]
     * @Date   2018-10-17T15:48:02+0800
     * @Author MaWei
     * @Link   http://www.mawei.live
     */
    static function getOrderSN(){
        list($usec, $sec) = explode(" ", microtime());
        $sn = ORDER_PREFIX. date('YmdHis'). intval($usec*1000000);
        return self::getStrByTime(ORDER_PREFIX);
    }

    /**
     * 创建文件夹
     * @param  string $_path 文件夹路径
     * @return array
     * @author MaWei (http://www.mawei.live)
     * @date 2014-8-3  下午2:10:22
     */
    static function createDir($_path){
        if (!file_exists($_path)){
            self::createDir(dirname($_path));
            mkdir($_path, 0777);
        }
    }

    /**
     * 返回文件的后缀名
     * @param string $_file
     * @return string $exname
     * @author MaWei ( http://www.mawei.live )
     * @date 2014-4-17 下午1:50:15
     */
    static function getFileExeName($_file){
        $file = basename($_file);
        $exname = substr(strrchr($file,'.'),1);
        return trim(strtolower($exname));
    }

    /**
     * 函数用于过滤标签，输出没有html的干净的文本
     * @param string text 文本内容
     * @return string 处理后内容
     */
    static function text($text){
        $text = nl2br($text);
        $text = real_strip_tags($text);
        $text = addslashes($text);
        $text = trim($text);
        return $text;
    }


    /**
     * 过滤表情
     * @param  string $_string 字符串
     * @return [type] [description]
     * @Date   2019-06-18T16:49:16+0800
     * @Author MaWei
     * @Link   http://www.mawei.live
     */
    static function removeEmoji($_string) {
        $string = json_encode($_string);
        $string = preg_replace("/\\\u[ed][0-9a-f]{3}\\\u[ed][0-9a-f]{3}/"," ",$string);//替换成*
        return json_decode($string);
    }

    /**
     * 把字符串写成文件
     * @param string $_str 要写入字符串
     * @param string $_path 文件路径名称
     * @param int $_type 0为复写,1.为添写
     * @return int|boolean
     * @author MaWei (http://www.mawei.live)
     * @date 2015-1-8 下午4:48:29
     */
    static function writeFile($_str,$_path,$_type = 0){
        $status = null;
        if(!$_type){
            $status = file_put_contents($_path, $_str);
        }else{
            $f = fopen($_path, 'a');
            //写入日志时间
            $status = fwrite($f, $_str);
            //写入换行
            fclose($f);
        }
        return $status;
    }

    /**
     * 写入日志
     * @param  string $_str 要写入字符串
     * @param  string $_logName 日志名称
     * @return [type] [description]
     * @Date   2019-01-09T14:21:42+0800
     * @Author MaWei
     * @Link   http://www.mawei.live
     */
    static function writeLog($_str,$_logName = 'log.txt',$_des = ''){
        //日志路径
        $filePath = ROOT_PATH.'/uploads/log';
        //创建目录
        self::createDir($filePath);
        $filePath .= '/'.$_logName.'-'.date('Y-m-d').'.txt';
        //提取debug信息
        $debugInfo = debug_backtrace();
        //转成字符
        $str = is_string($_str) ? $_str : json_encode($_str);
        //创建文档对象
        $f = fopen($filePath, 'a');
        //写入日志时间
        fwrite($f, "<---------++Date:  ".date('Y-m-d H:i:s').'------------>');
        fwrite($f, "\r\nFile:".$debugInfo[0]['file']);
        fwrite($f, "\r\nLine:".$debugInfo[0]['line']);
        fwrite($f, "\r\nFunction:".$debugInfo[1]['function']);
        fwrite($f, "\r\nArgs:".json_encode($debugInfo[0]['args']));
        fwrite($f, "\r\nLog Description:".$_des);
        fwrite($f, "\r\nLogMsg:".$str);
        //写入换行
        fwrite($f, "\r\n");
        //关闭文档对象
        fclose($f);
        //销毁数据
        unset($debugInfo);
    }

    /**
     * 读取文件
     * @param string $_path 文件路径
     * @param string|int $_type 读取类型 'array'读r成数组,'0'读取全部为字符串,'2'读取多少
     * @param string $_charset 输出字符格式
     * @return string
     * @author MaWei (http://www.mawei.live)
     * @date 2015-1-12 下午4:09:42
     */
    static function rFile($_path,$_type = 'array',$_charset = null){
        if(file_exists($_path)){
            if((string)$_type == 'array'){
                return file($_path,FILE_SKIP_EMPTY_LINES|FILE_IGNORE_NEW_LINES);
            }elseif($_type == 0){
                return file_get_contents($_path);
            }elseif(is_int($_type) && $_type > 0){
                $f = fopen($_path, 'r');
                $str = fread($f, $_type);
                fclose($f);
                return $_charset ? self::autoCharset($str) : $str;
            }else{
                return null;
            }
        }else{
            return null;
        }
    }

    /**
     * 返回两到三层的树形菜单
     * @param  array $_list
     * @param  int $_level
     * @return array
     * @author MaWei (http://www.mawei.live)
     * @date 2018-08-01 16:37:16
     */
    static function getTree($_list,$_level = 2,$_childrenkey = 'id'){
        $pid = $pid2 = null;
        $tree = [];
        foreach ($_list as $k => $v){
            if($v['pid'] == 0){
                $pid = $k;
                $tree[$k] = $v;
            }else{
                if($_level == 2 || $v['level'] == 1){
                    $tree[$pid]['children'][$k] = $v;
                    $tree[$pid]['childrenkey'][] = $v[$_childrenkey];
                    $pid2 = $v['pid'];
                }elseif($_level == 3 || $v['level'] > 1){
                    $tree[$pid]['children'][$pid2][$k] = $v;
                    $tree[$pid]['childrenkey'][] = $v[$_childrenkey];
                }
            }
        }
        return $tree;
    }

    /**
     * 把数组有PID的层次化
     * @param  array $_list
     * @param  int $_pid
     * @return array
     * @author MaWei (http://www.mawei.live)
     * @date 2018-08-01 16:37:12
    */
    static function level($_menu,$_pid = 0,$_level=0,$_flag = 1){
        static $level = [];
        $_flag && $level = [] && $_flag = 0;
        foreach ($_menu as $k => $v){
            if($v['pid'] == $_pid){
                $level[$v['id']] = $v;
                $level[$v['id']]['level'] = $_level;
                $level[$v['id']]['levelstr'] = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;|----', $_level);
                unset($_menu[$k]);
                self::level($_menu,$v['id'],$_level+1,0);
            }
        }
        return $level;
    }

    /**
     * 自动转换字符集 支持数组转换
     * @param  string $string 要转换的字符
     * @param  string $from 要转换的字符字符编码
     * @param  string $to 转换成字符编码
     * @return array
     * @author MaWei (http://www.mawei.live)
     * @date 2016年6月2日 下午3:43:39
     */
    static function autoCharset($string, $from='gbk', $to='utf-8') {
        $from = strtoupper($from) == 'UTF8' ? 'utf-8' : $from;
        $to = strtoupper($to) == 'UTF8' ? 'utf-8' : $to;
        if (strtoupper($from) === strtoupper($to) || empty($string) || (is_scalar($string) && !is_string($string))) {
            //如果编码相同或者非字符串标量则不转换
            return $string;
        }
        if (is_string($string)) {
            if(mb_detect_encoding($string,array('ASCII', 'GB2312', 'GBK', 'UTF-8')) == strtoupper($to)){
                return $string;
            }elseif (function_exists('mb_convert_encoding')) {
                return mb_convert_encoding($string, $to, $from);
            } elseif (function_exists('iconv')) {
                return iconv($from, $to, $string);
            } else {
                return $string;
            }
        } elseif (is_array($string)) {
            foreach ($string as $key => $val) {
                $_key = autoCharset($key, $from, $to);
                $string[$_key] = autoCharset($val, $from, $to);
                if ($key != $_key)
                    unset($string[$key]);
            }
            return $string;
        }
        else {
            return $string;
        }
    }

    /**
     * 产生随机字串，可用来自动生成密码
     * 默认长度6位 字母和数字混合 支持中文
     * @param string $len 长度
     * @param string $type 字串类型
     * 0 字母 1 数字 其它 混合
     * @param string $addChars 额外字符
     * @return string
     */
    static function randString($len=6,$type='',$addChars='') {
        $len = is_int($len) ? $len : rand($len[0],$len[1]);
        $str ='';
        switch($type) {
            case 0:
                $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.$addChars;
                break;
            case 1:
                $chars= str_repeat('0123456789',3);
                break;
            case 2:
                $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ'.$addChars;
                break;
            case 3:
                $chars='abcdefghijklmnopqrstuvwxyz'.$addChars;
                break;
            case 4:
                $chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借".$addChars;
                break;
            default :
                // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
                $chars='ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'.$addChars;
                break;
        }
        if($len>10 ) {//位数过长重复字符串一定次数
            $chars= $type==1? str_repeat($chars,$len) : str_repeat($chars,5);
        }
        if($type!=4) {
            $chars   =   str_shuffle($chars);
            $str     =   substr($chars,0,$len);
        }else{
            // 中文随机字
            for($i=0;$i<$len;$i++){
                $str.= static::msubstr($chars,1, floor(mt_rand(0,mb_strlen($chars,'utf-8')-1)),'utf-8',false);
            }
        }
        return $str;
    }

    /**
     * 字符串截取，支持中文和其他编码
     * @static
     * @access public
     * @param string $str 需要转换的字符串
     * @param string $start 开始位置
     * @param string $length 截取长度
     * @param string $charset 编码格式
     * @param string $suffix 截断显示字符
     * @return string
     */
    static function msubstr($str, $length, $start=0, $charset="utf-8", $suffix=true) {
        if(function_exists("mb_substr")){
            $slice = mb_substr($str, $start, $length, $charset);
        }elseif(function_exists('iconv_substr')) {
            $slice = iconv_substr($str,$start,$length,$charset);
        }else{
            $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            preg_match_all($re[$charset], $str, $match);
            $slice = join("",array_slice($match[0], $start, $length));
        }
        return $suffix ? $slice.'...' : $slice;
    }

    /**
     * 友好的时间显示
     *
     * @param int    $sTime 待显示的时间
     * @param string $type  类型. normal | mohu | full | ymd | other
     * @param string $alt   已失效
     * @return string
     */
    static function friendlyDate($sTime,$type = 'normal',$alt = 'false') {
        if (!$sTime)
            return '';
        //sTime=源时间，cTime=当前时间，dTime=时间差
        $cTime      =   time();
        $dTime      =   $cTime - $sTime;
        $dDay       =   intval(date("z",$cTime)) - intval(date("z",$sTime));
        //$dDay     =   intval($dTime/3600/24);
        $dYear      =   intval(date("Y",$cTime)) - intval(date("Y",$sTime));
        //normal：n秒前，n分钟前，n小时前，日期
        if($type=='normal'){
            if( $dTime < 60 ){
                if($dTime < 10){
                    return '刚刚';    //by yangjs
                }else{
                    return intval(floor($dTime / 10) * 10)."秒前";
                }
            }elseif( $dTime < 3600 ){
                return intval($dTime/60)."分钟前";
                //今天的数据.年份相同.日期相同.
            }elseif( $dYear==0 && $dDay == 0  ){
                //return intval($dTime/3600)."小时前";
                return '今天'.date('H:i',$sTime);
            }elseif($dYear==0){
                return date("m月d日 H:i",$sTime);
            }else{
                return date("Y-m-d H:i",$sTime);
            }
        }elseif($type=='mohu'){
            if( $dTime < 60 ){
                return $dTime."秒前";
            }elseif( $dTime < 3600 ){
                return intval($dTime/60)."分钟前";
            }elseif( $dTime >= 3600 && $dDay == 0  ){
                return intval($dTime/3600)."小时前";
            }elseif( $dDay > 0 && $dDay<=7 ){
                return intval($dDay)."天前";
            }elseif( $dDay > 7 &&  $dDay <= 30 ){
                return intval($dDay/7) . '周前';
            }elseif( $dDay > 30 ){
                return intval($dDay/30) . '个月前';
            }
            //full: Y-m-d , H:i:s
        }elseif($type=='full'){
            return date("Y-m-d , H:i:s",$sTime);
        }elseif($type=='ymd'){
            return date("Y-m-d",$sTime);
        }else{
            if( $dTime < 60 ){
                return $dTime."秒前";
            }elseif( $dTime < 3600 ){
                return intval($dTime/60)."分钟前";
            }elseif( $dTime >= 3600 && $dDay == 0  ){
                return intval($dTime/3600)."小时前";
            }elseif($dYear==0){
                return date("Y-m-d H:i:s",$sTime);
            }else{
                return date("Y-m-d H:i:s",$sTime);
            }
        }
    }

    /**
     * 加密
     * [encrypt description]
     * @return [type] [description]
     * @Date   2018-08-04T14:26:14+0800
     * @Author MaWei
     * @Link   http://www.mawei.live
     */
    static function pwdEncrypt($_str,$_prefix = 'xzd'){
        return sha1($_prefix.$_str);
    }

    /**
     * 压缩图片
     * @param $image　图片的绝对路径，
     * @param null $rate　压缩等级
     * @return bool
     */
    static function zip_image($image,$rate=null){
        if ( ! is_file($image) ) return false;
        if ( strpos($image,'_85') || strpos($image,'_ori') ) return false;

        if ( ! function_exists('imagecreatefromjpeg') ) return ;

        $path_arr = pathinfo($image);
        copy( $image,(($path_arr['dirname']=='.')?'':$path_arr['dirname']).'/'.$path_arr['filename'].'_ori.'.$path_arr['extension'] );
        if ( $path_arr['extension']=='jpg' || $path_arr['extension']=='jpeg' || $path_arr['extension']=='JPG' || $path_arr['extension']=='JPEG' ) {
            $m = imagecreatefromjpeg($image);
        } elseif ( $path_arr['extension']=='gif' ) {
            $m = imagecreatefromgif($image);
        } else {
            $m = imagecreatefrompng($image);
        }
        if ( !isset($rate) ) {
            $re = imagejpeg($m,(($path_arr['dirname']=='.')?'':$path_arr['dirname']).'/'.$path_arr['filename'].'_85.'.$path_arr['extension'],85);
            $re = imagejpeg($m,$image,50);
        }
        imagedestroy($m);
        return $re;
    }

    /**
     * 短字符加密函数
     * @param string $txt 需要加密的字符串
     * @param string $_key 密钥
     * @return string 返回加密结果
     */
    static function revEncrypt($_data, $_key = 'fee0a99242beeebf3088e42893c62466f25c38ea'){
        if(!$_data){
            return '';
        }
        $encryption_key = base64_decode($_key);
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($_data, 'aes-256-cbc', $encryption_key, 0, $iv);
        return base64_encode($encrypted . '::' . $iv);
    }

    /**
     * 短字符解密函数
     * @param string $txt  需要解密的字符串
     * @param string $_key 密匙
     * @return string 字符串类型的返回结果
     */
    static function revDecrypt($_str, $_key = 'fee0a99242beeebf3088e42893c62466f25c38ea'){
        if(!$_str){
            return '';
        }
        $encryption_key = base64_decode($_key);
        list($encrypted_data, $iv) = explode('::', base64_decode($_str), 2);
        return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
    }
}