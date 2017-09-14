<?php
namespace backend\models;

use yii\base\Model;

class LoginForm extends Model{
    public $username;
    public $login_pwd;
    public $code;
    public $remember;
    public function rules()
    {
        return [
            [['username','login_pwd'],'required'],
            ['code','captcha','captchaAction'=>'admin/captcha'],
            ['remember','safe']

        ];
    }
    public function attributeLabels()
    {
        return [
            'username'=>'用户名',
            'login_pwd'=>'密码',
            'code'=>'验证码',
            'remember'=>'记住我'
        ];
    }
}