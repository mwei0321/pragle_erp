<?php

/*
 * 邮件相关定时任务
 * @Author: MaWei 
 * @Date: 2022-02-13 20:20:18 
 * @Last Modified by: MaWei
 * @Last Modified time: 2022-02-13 20:56:14
 */

namespace system\services\graphic;

use yii\db\Query;
use system\common\{HelperFuns, TableMap, ServiceFactory};
use system\beans\cron\CronEmailBeans;

class EmailCronServices
{

    function actionEmail(CronEmailBeans $_cronEmailBeans)
    {
        switch ($_cronEmailBeans->cycle) {
            case '1':

                break;

            default:
                # code...
                break;
        }
    }

    function getEmailStatistics(CronEmailBeans $_cronEmailBeans)
    {
    }
}
