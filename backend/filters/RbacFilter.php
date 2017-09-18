<?php
namespace backend\filters;

use yii\base\ActionFilter;
use yii\web\ForbiddenHttpException;

class RbacFilter extends ActionFilter{
    public  function beforeAction($action)
    {
        if(!\Yii::$app->user->can($action->uniqueId)){
            if(\Yii::$app->user->isGuest){
                return $action->controller->redirect(\Yii::$app->user->loginUrl)->send();
            }
            throw new ForbiddenHttpException('你没有权限操作');
        }

        return parent::beforeAction($action);
    }
}