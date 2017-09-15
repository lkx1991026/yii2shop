<?php
namespace backend\models;


use yii\base\Model;

class AddRole extends Model{
    public $name;
    public $description;
    public $oldname;
    public $permissions;
    const SCENARIO_ADD='add';
    const SCENARIO_EDIT='edit';
    public function rules()
    {
        return [
            [['name','description'],'required'],
            ['name','checkName','on'=>self::SCENARIO_ADD],
            ['name','only','on'=>self::SCENARIO_EDIT],
            ['oldname','safe','on'=>self::SCENARIO_EDIT],
            ['permissions','safe']

        ];
    }
    public function checkName(){
        if(\Yii::$app->authManager->getRole($this->name)){
            $this->addError('name','角色名已存在');
        }
    }
    public function only(){
        if($this->name!=$this->oldname && \Yii::$app->authManager->getRole($this->name)){
            $this->addError('name','角色名已存在');
        }

    }
    public function attributeLabels()
    {
        return [
            'name'=>'角色名',
            'description'=>'描述'
        ];
    }
    public static function getChildrenByRole($name){
        $children=\Yii::$app->authManager->getPermissionsByRole($name);
        $items=[];
        foreach($children as $child){
//            var_dump($child);exit;
            $items[$child->description]=$child->name;
        }
        return $items;
    }
}

