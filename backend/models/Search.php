<?php
namespace backend\models;


use yii\base\Model;

class Search extends Model{
    public $name;
    public $max;
    public $min;
    public $sn;
    public function rules()
    {
        return[
            ['name','string'],
            [['sn','min','max'],'integer'],

        ];
    }
    public static function getQuery($model){
        $query=Goods::find();
        $query->andFilterWhere(['like','name',$model->name]);
        $query->andFilterWhere(['like','sn',$model->sn]);
        $query->andFilterWhere(['>','market_price',$model->min]);
        $query->andFilterWhere(['<','market_price',$model->max]);
        $query->andFilterWhere(['=','status',1]);
        return $query;
    }
    public function attributeLabels()
    {
        return [
            'name'=>'商品名称',
            'sn'=>'货号',
            'min'=>'最小价格',
            'max'=>'最大价格'
        ];
    }
}