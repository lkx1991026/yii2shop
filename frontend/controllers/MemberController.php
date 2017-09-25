<?php

namespace frontend\controllers;

use frontend\models\Locations;
use frontend\models\LoginForm;
use frontend\models\Member;
use frontend\models\SmsDemo;

class MemberController extends \yii\web\Controller
{
    public function actionRegister(){
        $model=new Member();
        $request=\Yii::$app->request;
        if($request->isPost){
            $model->load($request->post(),'');
//            var_dump($model);
//            var_dump($model->validate());exit;

            if($model->validate()){
//                var_dump($model->errors);exit;
                $model->save(false);
                return $this->redirect(['member/login']);
            };
        }
        return $this->renderPartial('register');
    }
    public function actionLogin(){
        $model=new LoginForm();
        $request= \Yii::$app->request;
        if($request->isPost){
            $model->load($request->post(),'');
            $remember=$model->remember;
//            var_dump($remember);exit;
            if($model->validate()){
                $user=Member::findOne(['username'=>$model->username]);
                if($user && \Yii::$app->security->validatePassword($model->password,$user->password_hash)){

                    $user->last_login_ip=\Yii::$app->request->getUserIP();
                    $user->last_login_time=time();
                    $user->save(false);
                    \Yii::$app->user->login($user,$remember?3600:0);
                    Member::CookieToTable();

                    return $this->redirect(['index/index']);
                }
            }
        }
        return $this->renderPartial('login');
    }
    public function actionIndex()
    {
        return $this->render('index');
    }
    public function actionCheckName($username){
        $model=Member::findOne(['username'=>$username]);
        if($model){
            return 'false';
        }else{
            return 'true';
        }
    }
    public function actionCheckEmail($email){
        $model=Member::findOne(['email'=>$email]);
        if($model){
            return 'false';
        }else{
            return 'true';
        }
    }
    public function actionCheckTel($tel){
        $model=Member::findOne(['tel'=>$tel]);
        if($model){
            return 'false';
        }else{
            return 'true';
        }
    }
    public function actionSms($telnum){
        $mem=Member::find()->where(['tel'=>$telnum])->one();
        if($mem || strlen($telnum)!=11){
            return json_encode(
                ['success'=>'false']
            );
        }else{
            $rand=rand(1000,9999);
            $demo = new SmsDemo(
                "LTAI77hnmSBcXeRv",
                "VNlLeMCUgkjf4AFRgsQix78eySr4Oy"
            );

            echo "SmsDemo::sendSms\n";
            $response = $demo->sendSms(
                "雷婷", // 短信签名
                "SMS_97945009", // 短信模板编号
                "{$telnum}", // 短信接收者
                Array(  // 短信模板中字段的值
                    "code"=>"{$rand}",
                )
            );
            $redis = new \Redis();
            $redis->connect('127.0.0.1');
            $redis->set("{$telnum}","{$rand}");
        }

    }
    public function actionCheckCaptcha($captcha,$telnum){
        $redis = new \Redis();
        $redis->connect('127.0.0.1');
        $captcha1=$redis->get("{$telnum}");
        if($captcha==$captcha1){
            return 'true';
        }else{
            return 'false';
        }
    }
    public function actionLogout(){
        \Yii::$app->user->logout();
        return $this->redirect(['index/index']);
    }
}
