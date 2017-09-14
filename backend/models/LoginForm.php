<?php
namespace backend\models;

use yii\base\Model;

class LoginForm extends Model{
    public $username;
    public $password;
    public $code;
    public $remember;
    public function rules()
    {
        return [
            [['username','password'],'required'],
            ['code','captcha','captchaAction'=>'admin/captcha'],
            ['remember','safe']

        ];
    }
}