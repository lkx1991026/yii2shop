<?php

namespace backend\controllers;

use backend\models\Admin;
use backend\models\LoginForm;
use yii\captcha\CaptchaAction;
use yii\web\Cookie;

class AdminController extends \yii\web\Controller
{
    public function actionAdd(){
        $model =new Admin();
        $request=\Yii::$app->request;
        $model->scenario=Admin::SCENARIO_ADD;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $model->save();
                return $this->redirect(['/admin/index']);
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    public function actionIndex()
    {
        $models=Admin::find()->where(['!=','status',-1])->all();
        return $this->render('index',['models'=>$models]);
    }
    public function actionEdit($id){
        $model=Admin::find()->where('id='.$id)->one();
        $request=\Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $model->save();
                \Yii::$app->session->setFlash('success','修改成功!');
                return $this->redirect(['admin/index']);
            }else{
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    public function actionChangepwd($id){
            $model=Admin::findOne(['id'=>$id]);
            $model->scenario=Admin::SCENARIO_CHANGEPWD;
            $request=\Yii::$app->request;
            if($request->isPost){
                $model->load($request->post());
                if($model->validate()){
                    if($model->save()){
                        \Yii::$app->user->logout();
                        \Yii::$app->session->setFlash('success','修改密码成功,请重新登陆!');
                        return $this->redirect(['admin/login']);
                    }else{
                        \Yii::$app->session->setFlash('success','旧密码输入错误,请重试!');
                        return $this->goBack(['admin/changepwd?id='.$id]);
                    }

                }

            }
            return $this->render('changepwd',['model'=>$model]);
    }
    public function actionDelete($id){
        $model=Admin::find()->where('id='.$id)->one();
        $model->status=-1;
        var_dump($model->save(false));
        \Yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['admin/index']);
    }
    public function actionLogin()
    {
        //创建登陆表单模型
        $model = new LoginForm();

        $request = \Yii::$app->request;
        //表单提交/载入数据
        if ($request->isPost) {
            $model->load($request->post());
            //数据验证
            $remember=$model->remember;
//            var_dump($model);exit;
//            var_dump($model->validate());
//            var_dump($model->errors);
//            exit;
            if ($model->validate()) {
               //从数据库查询数据

                $user = Admin::find()->where(['=', 'username', $model->username])->one();
                //判断数据,数据存在且表单密码与数据库密码匹配
                if ($user && \Yii::$app->security->validatePassword($model->login_pwd, $user->password_hash)) {
                    //更新最后登陆时间和最后登陆IP
                    //将用户数据存入login()中,却提示数据未实现接口,解决:用户验证配置未改!~~
                    \Yii::$app->user->login($user,$remember?3600:0);
                    $user->last_login_time=time();
                    $user->last_login_ip=\Yii::$app->request->getUserIP();
                    $user->save(false);
                    \Yii::$app->session->setFlash('success', '登陆成功');
                    return $this->redirect(['admin/index']);
                } else {
                    \Yii::$app->session->setFlash('success', '用户名密码错误');
                    return $this->redirect(['admin/login']);
                }
            }

        }
        return $this->render('login', ['model' => $model]);
    }
    public function actionTest(){
        var_dump(\Yii::$app->user->isGuest);
        var_dump(\Yii::$app->user);
    }
    public function actionLogout()
    {   if(!\Yii::$app->user->isGuest){
        \Yii::$app->user->logout();
        \Yii::$app->session->setFlash('success','退出登陆成功!');
        return $this->redirect(['login']);
    }else{
        \Yii::$app->session->setFlash('success','请先登陆!');
        return $this->redirect(['login']);
    }
    }
    public function actions()
    {
        return [
            'captcha'=>[
                'class'=>CaptchaAction::className(),
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'minLength'=>3,
                'maxLength'=>3,
            ]
        ];
    }


}
