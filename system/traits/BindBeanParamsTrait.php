<?php

/**
 * @Author: MaWei
 * @Date:   2021-12-19
 * @Last Modified by:   mawei
 * @Last Modified time: 2021-12-23
 */

namespace system\traits;

use Yii;
use yii\base\InlineAction;
use yii\web\BadRequestHttpException;
use system\common\BeanInterface;

trait BindBeanParamsTrait
{
    /**
     * Binds the parameters to the action.
     * This method is invoked by [[\yii\base\Action]] when it begins to run with the given parameters.
     * This method will check the parameter names that the action requires and return
     * the provided parameters according to the requirement. If there is any missing parameter,
     * an exception will be thrown.
     * @param \yii\base\Action $action the action to be bound with parameters
     * @param array $params the parameters to be bound to the action
     * @return array the valid parameters that the action can run with.
     * @throws BadRequestHttpException if there are missing or invalid parameters.
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     */
    public function bindActionParams($action, $params)
    {
        if ($action instanceof InlineAction) {
            $method = new \ReflectionMethod($this, $action->actionMethod);
        } else {
            $method = new \ReflectionMethod($action, 'run');
        }
        $params = array_merge($params, Yii::$app->getRequest()->getBodyParams());
        $args = [];
        $missing = [];
        foreach ($method->getParameters() as $param) {
            $name = $param->getName();
            if (array_key_exists($name, $params)) {
                if ($param->isArray()) {
                    $args[] = (array)$params[$name];
                } elseif (!is_array($params[$name])) {
                    $args[] = $params[$name];
                } elseif (
                    ($paramClass = $param->getClass())
                    && ($paramClass->implementsInterface(BeanInterface::class))
                ) {
                    $args[] = $params[$name] = Yii::createObject(['class' => $paramClass->getName()] + $params[$name]);
                } else {
                    throw new BadRequestHttpException(Yii::t('yii', 'Invalid data received for parameter "{param}".', [
                        'param' => $name,
                    ]));
                }
            } elseif (
                ($paramClass = $param->getClass())
                && ($paramClass->implementsInterface(BeanInterface::class))
            ) {
                $args[] = $params[$name] = Yii::createObject(['class' => $paramClass->getName()] + $params);
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $params[$name] = $param->getDefaultValue();
            } else {
                $missing[] = $name;
            }
        }

        if (!empty($missing)) {
            throw new BadRequestHttpException(Yii::t('yii', 'Missing required parameters: {params}', [
                'params' => implode(', ', $missing),
            ]));
        }

        $this->actionParams = $args;

        return $args;
    }
}
