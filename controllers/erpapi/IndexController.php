<?php

/**
 * @Author: MaWei
 * @Date:   2021-12-19
 * @Last Modified by:   mawei
 * @Last Modified time: 2021-12-22
 */

namespace app\controllers\erpapi;

use Yii;
use Closure;
use app\controllers\InitController;
use yii\web\Controller;
use services\common\{ServiceFactory, TableMap};
use services\traits\BindBeanParamsTrait;
use services\beans\KpiBeans;


class ab
{

    function dd(object $da)
    {
        var_dump($da->aa);
    }
}

class at
{
}

class IndexController extends Controller
{

    use BindBeanParamsTrait;

    function actionIndex(KpiBeans $kpiData)
    {
        $request = Yii::$app->request;

        $obj = (object)['aa' => 1, 'bb' => 2];
        $obj->aa = 11;
        $obj->bb = 222;
        var_dump($obj);
        var_dump(isset($obj->cc));


        $abobj = new ab();
        $abobj->dd($obj);

        exit();

        // var_dump($kpiData);

        // 初始化数据库
        // $dbObj = ServiceFactory::getSrvObj('BaseDB',TableMap::Config);


        // var_dump($dbObj->getInfoById(90));
        // exit();


        return $this->reJson(['a' => 1111]);
    }
}
