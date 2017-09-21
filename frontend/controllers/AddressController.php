<?php
namespace frontend\controllers;

use frontend\models\Address;
use frontend\models\Locations;
use yii\web\Controller;

class AddressController extends Controller{
    public function actionIndex(){
        $addrs=Address::getAddrsByUserId();
//        var_dump($addrs);exit;

        $model=Locations::find()->where(['parent_id'=>0])->all();

        return $this->renderPartial('index',['model'=>$model,'addrs'=>$addrs]);
    }
    public function actionGetAddress($id){
        $data=Locations::find()->where(['parent_id'=>$id])->asArray()->all();
        return json_encode($data);
    }
    public function actionAdd(){
        $request=\Yii::$app->request;
        if($request->isPost){
            if($request->post('addr_id')){
                $model=Address::find()->where(['id'=>$request->post('addr_id')])->one();
                $model->load($request->post(),'');
            }else{
                $model=new Address();
                $model->load($request->post(),'');
                $model->user_id=\Yii::$app->user->getId();
            }
            $model->save();
            return $this->redirect(['address/index']);
        }
    }
        public function actionEdit($id){
        $model=Address::find()->where(['id'=>$id])->asArray()->one();
        return json_encode($model);
        }
        public function actionGetAddrs($id){
            $addrs=Locations::find()->where(['parent_id'=>$id])->asArray()->all();
//            var_dump($addrs);exit;
            return json_encode($addrs);
        }
        public function actionChangeDefault($id){
            $addr=Address::find()->where(['id'=>$id])->one();
            $addr->is_default_addr=!$addr->is_default_addr;
            $addr->save();
            return json_encode(
                ['data'=>$addr->is_default_addr]
            );
        }
        public function actionDelete($id){
            $addr=Address::find()->where(['id'=>$id])->one();
            if($addr->delete()){
                return json_encode(
                    ['success'=>true]
                );
            }else{
                return json_encode(
                    ['success'=>false]
                );
            }
        }
}