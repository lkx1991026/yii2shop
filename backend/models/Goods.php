<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "goods".
 *
 * @property integer $id
 * @property string $name
 * @property string $sn
 * @property string $logo
 * @property integer $goods_catgory_id
 * @property integer $brand_id
 * @property string $market_price
 * @property string $shop_price
 * @property integer $stock
 * @property integer $is_on_sale
 * @property integer $status
 * @property integer $sort
 * @property integer $create_time
 * @property integer $view_times
 */
class Goods extends \yii\db\ActiveRecord
{
    public $min;
    public $max;
    public $search_name;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_catgory_id', 'brand_id', 'stock', 'is_on_sale', 'status', 'sort'], 'integer'],
            [[ 'brand_id', 'stock', 'is_on_sale', 'status', 'sort'], 'required'],
            [['market_price', 'shop_price'], 'number'],
            [['name', 'sn'], 'string', 'max' => 20],
            [['logo'], 'string', 'max' => 255],
            ['goods_catgory_id','required','message'=>'请选择商品分类!'],
            ['goods_catgory_id','checkdepth'],
            [['min','max'],'integer'],
            ['search_name','string']

        ];
    }
    public function checkdepth(){
        $depth=GoodsCategory::find()->where(['id'=>$this->goods_catgory_id])->andWhere(['!=','depth',2])->one();
        if($depth){
            $this->addError('goods_catgory_id','商品分类必须为三级分类');
        }

    }

    /**
     * @inheritdoc
     */

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '商品名称',
            'sn' => '货号',
            'logo' => 'logo图片',
            'goods_catgory_id' => '商品分类id',
            'brand_id' => '品牌分类',
            'market_price' => '市场价格',
            'shop_price' => '商品价格',
            'stock' => '库存',
            'is_on_sale' => '1在售0下架',
            'status' => '状态1正常0回收站',
            'sort' => '排序',
            'create_time' => '添加时间',
            'view_times' => '浏览次数',
        ];
    }
    public function getCat(){
        return $this->hasOne(GoodsCategory::className(),['id'=>'goods_catgory_id']);
    }
    public function getBrand(){
        return $this->hasOne(Brand::className(),['id'=>'brand_id']);
    }
    public function getIntro(){
        return $this->hasOne(GoodsIntro::className(),['goods_id'=>'id']);
    }
}
