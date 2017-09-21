<?php
namespace frontend\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class Member extends ActiveRecord implements IdentityInterface
{   public $repassword;
    public $password;
    public $checkcode;
    public $captcha;

    public function rules()
    {
        return [
            [['username','password','repassword','tel','email'],'required'],
            ['email','email'],
            ['repassword','compare','compareAttribute'=>'password'],
            [['username','tel','email'],'unique'],
            ['captcha','checkCaptcha'],
            ['checkcode','captcha','captchaAction'=>'site/captcha']
        ];
    }
    public function checkCaptcha(){
        $redis=new \Redis();
        $redis->connect('127.0.0.1');
        $rst=$redis->get($this->tel);
        if($rst && $rst=$this->captcha){
            return false;
        }

    }
    public function beforeSave($insert)
    {
        if($insert){
            $this->auth_key=\Yii::$app->security->generateRandomString();
            $this->password_hash=\Yii::$app->security->generatePasswordHash($this->password);
            $this->created_at=time();
            $this->status=1;

        }else{


        }
        return parent::beforeSave($insert);
    }
    public static function findIdentity($id)
    {
        return self::findOne(['id'=>$id]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
//        return self::findOne(['auth_key' => $token]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() == $authKey;
    }
    public static function CookieToTable(){
        $member_id=\Yii::$app->user->getId();
        $cookies=\Yii::$app->request->cookies;
        $cookie=$cookies->get('cart');
        $value=$cookies->getValue('cart');
        if($value){
            $carts=unserialize($value);
        }
        if(isset($carts)){
            foreach($carts as $key=>$cart){
                $model=Cart::find()->where(['=','goods_id',$key])->andWhere(['=','member_id',$member_id])->one();
                if($model){
                    $model->amount+=$cart;
                }else{
                    $model=new Cart();
                    $model->member_id=$member_id;
                    $model->goods_id=$key;
                    $model->amount=$cart;
                }
                $model->save();
                $cookies=\Yii::$app->response->cookies;
                $cookies->remove($cookie);
            }
        }


    }
}