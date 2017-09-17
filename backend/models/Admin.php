<?php

namespace backend\models;

use Yii;
use yii\web\IdentityInterface;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "admin".
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $last_login_time
 * @property string $last_login_ip
 */
class Admin extends \yii\db\ActiveRecord implements IdentityInterface
{
    public $password;
    public $oldpassword;
    public $newpassword;
    public $renewpassword;
    public $roles;
    const SCENARIO_ADD='add';
    const SCENARIO_EDIT='edit';
    const SCENARIO_CHANGEPWD='pwd';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'email'], 'required'],
            [['username', 'email'], 'string', 'max' => 255],
            [['username'], 'unique','message'=>'用户名已存在','on'=>[self::SCENARIO_ADD,self::SCENARIO_EDIT]],
            [['email'], 'unique'],
            ['email','email','message'=>'邮箱格式不正确','on'=>[self::SCENARIO_ADD,self::SCENARIO_EDIT]],
            ['password','required','on'=>self::SCENARIO_ADD,'message'=>'请输入密码'],
            ['password','string'],
            [['auth_key'], 'string', 'max' => 100],
//            [['repassword'],'compare','compareAttribute'=>'password','message'=>'两次输入密码不一致'],
            [['newpassword','renewpassword'],'required','on'=>self::SCENARIO_CHANGEPWD,'message'=>'请输入新密码'],
            ['renewpassword','compare','compareAttribute'=>'newpassword','message'=>'两次输入密码不一致','on'=>self::SCENARIO_CHANGEPWD],
            ['status','required','on'=>[self::SCENARIO_ADD,self::SCENARIO_EDIT]],
            ['oldpassword','checkPassword','on'=>self::SCENARIO_CHANGEPWD],
            ['oldpassword','required','message'=>'请输入密码','on'=>self::SCENARIO_CHANGEPWD],
            ['roles','safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function checkPassword(){
        if(!Yii::$app->security->validatePassword($this->oldpassword,Yii::$app->user->identity->password_hash)){
            $this->addError('oldpassword','密码错误');
        }
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '用户名',
            'auth_key' => '口令',
            'password' => '密码',
            'repassword' =>'确认密码',
            'email' => '邮箱',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'last_login_time' => '最后登录时间',
            'last_login_ip' => '最后登录ip',
            'newpassword'=>'新密码',
            'renewpassword'=>'重复新密码',
            'roles'=>'角色'
        ];
    }
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->auth_key = \Yii::$app->security->generateRandomString();
            $this->created_at = time();
            $this->password_hash = \Yii::$app->security->generatePasswordHash($this->password);
        } else {
            $this->updated_at = time();
            if($this->password){
                $this->password_hash = Yii::$app->security->generatePasswordHash($this->password);
                $this->auth_key = Yii::$app->security->generateRandomString();

            }
//            if(empty($this->login_pwd)){
//                $this->last_login_time = time();
//                $this->last_login_ip = \Yii::$app->request->getUserIP();
//            }

            }


        return parent::beforeSave($insert);
    }

    public static function findIdentity($id)
    {
        return self::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return self::findOne(['auth_key' => $token]);
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
        return $this->auth_key === $authKey;
    }
    public static function getRoles(){
        $roles=Yii::$app->authManager->getRoles();
        $items=[];
        foreach($roles as $role){
            $items[$role->name]=$role->description;
        }
        return $items;
    }
}
