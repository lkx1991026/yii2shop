<?php
namespace frontend\models;


use yii\base\Model;

class LoginForm extends Model{
    public $username;
    public $password;
    public $checkcode;
    public $remember;
    public function rules()
    {
        return [
            [['username','password','checkcode'],'required'],
            ['remember','safe']
        ];
    }

}