<?php

/**
 * @Author: MaWei
 * @Date:   2021-12-19
 * @Last Modified by:   mawei
 * @Last Modified time: 2021-12-22
 */

namespace app\controllers\erpapi;

use yii\web\Controller;
use services\common\{ServiceFactory, TableMap};
use services\traits\BindBeanParamsTrait;
use system\common\HelperFuns;

class IndexController extends Controller
{

    function actionIndex()
    {
        $a = [
            "module/cj-srv/db/record.go",
            "module/cj-srv/handler/attend.go",
            "module/cj-srv/handler/user.go",
            "module/cj-srv/model/mc_self_check.go",
            "module/cj-srv/model/record.go",
            "module/cj-srv/model/result.go",




        ];

        $path = "F:/GoCode/ancda/cj-srv/";

        var_dump(HelperFuns::copyFile($path, $a));
    }
}
