<?php
namespace frontend\controllers;
use yii\web\Controller;

class SysController extends Controller{
    public function actionIndexToStatic(){
        $html=$this->renderPartial('@frontend/views/index/index.php');
        file_put_contents(\Yii::getAlias('@frontend/web/index.html'),$html);
        return $this->redirect('/index.html');
    }
    public function actionDetailToStatic(){

    }
}