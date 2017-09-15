<?php
namespace backend\models;

use yii\base\Model;

class AddPermissionForm extends Model{
    public $name;
    public $oldname;
    public $description;
    const SCENARIO_ADD='add';
    const SCENARIO_EDIT='edit';
    public function rules()
    {
        return [
            ['name','checkName','on'=>self::SCENARIO_ADD],
            ['name','only','on'=>self::SCENARIO_EDIT],
            [['description','name'],'required'],
            ['oldname','safe']
        ];
    }
    public function checkName(){
        if(\Yii::$app->authManager->getPermission($this->name)){
            $this->addError('name','权限名已存在');
        }
    }
    public function only(){
        if($this->oldname!=$this->name && \Yii::$app->authManager->getPermission($this->name)){
            $this->addError('name','权限名已存在');
        }
    }
    public function attributeLabels()
    {
        return [
            'name'=>'权限名(路由)',
            'description'=>'描述'
        ];
    }
    public static function getPermissions(){
        $permissions=\Yii::$app->authManager->getPermissions();
        $items=[];
        foreach($permissions as $permission){
            $items[$permission->name]=$permission->description;
        }
        return $items;
    }
}