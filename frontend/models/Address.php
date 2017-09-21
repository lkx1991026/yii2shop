<?php
namespace frontend\models;

use yii\db\ActiveRecord;

class Address extends ActiveRecord{
    public $addr_id;
    public function beforeSave($insert)
    {
        if($this->is_default_addr){
            Address::updateAll(['is_default_addr'=>0],['user_id'=>$this->user_id]);
        }
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function rules()
    {
        return [
            [['username','addr','tel','province_id','city_id','area_id','user_id','is_default_addr'],'required'],
            ['addr_id','safe']

        ];
    }
    public function getProvince(){
        return $this->hasOne(Locations::className(),['id'=>'province_id']);
    }
    public function getCity(){
        return $this->hasOne(Locations::className(),['id'=>'city_id']);
    }
    public function getArea(){
        return $this->hasOne(Locations::className(),['id'=>'area_id']);
    }
    public static function  getAddrsByUserId(){
        $addrs=Address::find()->where(['user_id'=>\Yii::$app->user->getId()])->all();
        $items=[];
        $i=0;
        $key=1;
        foreach($addrs as $addr){
            $items[$i]['id']=$addr->id;
            $items[$i]['default']=$addr->is_default_addr;
            $items[$i]['addr']=$key.'.&nbsp;'.$addr->username.'&nbsp;'.$addr->province->name.'&nbsp;'.$addr->city->name.'&nbsp;'.$addr->area->name.'&nbsp;'.$addr->addr.'&nbsp;'.$addr->tel;
            $i++;
            $key++;
        }
        return $items;
    }
}