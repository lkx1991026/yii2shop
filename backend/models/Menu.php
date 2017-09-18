<?php
namespace backend\models;

use yii\db\ActiveRecord;

class Menu extends ActiveRecord
{   public static $list=[];
    const SCENARIO_ADD= 'add';
    const SCENARIO_EDIT='edit';
    public function rules()
    {
        return[
            ['name','unique','on'=>self::SCENARIO_ADD],
            ['name','checkName','on'=>self::SCENARIO_EDIT],
            ['sort','integer'],
            ['parent_id','integer'],
            ['link','string']
        ];
    }
    public function checkName(){
        $old=Menu::findOne(['id'=>$this->id]);
        $oldname=$old->name;
        if($this->name!=$oldname && Menu::findOne(['name'=>$this->name])){
            $this->addError('name','菜单名已存在');
        }
    }
    public static function getMenuList(){
        $lists=self::find()->where('parent_id=0')->all();
        $items=[0=>'顶级菜单'];
        foreach($lists as $list){
            $items[$list->id]=$list->name;
        }
        return $items;
    }
    public function attributeLabels()
    {
        return [
            'name'=>'菜单名称',
            'parent_id'=>'上级分类',
            'sort'=>'排序',
            'link'=>'路由'
        ];
    }
    public static function getListByCat($model,$parent_id=0,$deep=0){
        foreach($model as $v){
            if($v['parent_id']==$parent_id){
                $v['name']=str_repeat('--',$deep*2).$v['name'];
                self::$list[]=$v;
                self::getListByCat($model,$v['id'],$deep+1);
            }
        }
        return self::$list;
    }
    public static function getMenus(){
            $menus=Menu::find()->where(['parent_id'=>0])->all();
            $menuitems=[];

            foreach($menus as $menu){
                $children=Menu::find()->where(['parent_id'=>$menu->id])->all();
                $items=[];
                foreach($children as $child){
                    if(\Yii::$app->user->can($child->link)){
                        $items[]=['label'=>$child->name,'url'=>[$child->link]];
                    }
                }
                if($items!=null){
                    $menuitems[]=['label'=>$menu->name,'items'=>$items];
                }

            }
            return $menuitems;
    }

}