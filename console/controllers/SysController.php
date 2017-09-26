<?php
namespace console\controllers;


use yii\console\Controller;

class SysController extends Controller{
    public function actionIndex(){
        while (true){
            echo date('ymd');
            sleep(1);
        }
    }
}