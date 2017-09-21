<?php

namespace backend\models;

use backend\models\CategoryQuery;
use creocoder\nestedsets\NestedSetsBehavior;
use Yii;

/**
 * This is the model class for table "goods_category".
 *
 * @property integer $id
 * @property integer $tree
 * @property integer $lft
 * @property integer $rgt
 * @property integer $depth
 * @property string $name
 * @property integer $parent_id
 * @property string $intro
 */
class GoodsCategory extends \yii\db\ActiveRecord
{
        public static $list=[];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[ 'depth', 'parent_id'], 'integer'],
            [['intro'], 'string'],
            [['name'], 'string', 'max' => 50],
            [['intro','name'],'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tree' => '树id',
            'lft' => '左值',
            'rgt' => '右值',
            'depth' => '层级',
            'name' => '名称',
            'parent_id' => '上级分类id',
            'intro' => '简介',
        ];
    }
    public function behaviors() {
        return [
            'tree' => [
                'class' => NestedSetsBehavior::className(),
                'treeAttribute' => 'tree',
                // 'leftAttribute' => 'lft',
                // 'rightAttribute' => 'rgt',
                // 'depthAttribute' => 'depth',
            ],
        ];
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public static function find()
    {
        return new CategoryQuery(get_called_class());
    }
    public static function getNodes(){
        $top=['id'=>0,'parent_id'=>0,'name'=>'顶级分类'];
        $categorys=self::find()->asArray()->select(['id','parent_id','name'])->all();
        array_unshift($categorys,$top);
        return json_encode($categorys);
    }

        public static function getchildren($arr,$parent_id=0,$deep=0){
            foreach($arr as $v){
                if($v['parent_id']==$parent_id){
                    $v['level']=str_repeat('---',$deep*2).$v['name'];
                    self::$list[]=$v;
                    self::getchildren($arr,$v['id'],$deep+1);
                }
            }
            return self::$list;
        }

}
