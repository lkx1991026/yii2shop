<?php

namespace backend\controllers;

use backend\models\Goods;
use backend\models\GoodsDayCount;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use flyok666\qiniu\Qiniu;
use flyok666\uploadifive\UploadAction;
use yii\data\Pagination;

class GoodsController extends \yii\web\Controller
{
    public function actionIndex()
    {   $count=Goods::find()->count();
        $pager=new Pagination(
            [
                'defaultPageSize'=>4,
                'totalCount'=>$count
            ]
        );
        $goods=Goods::find()->limit($pager->limit)->offset($pager->offset)->all();

        return $this->render('index',['goods'=>$goods,'pager'=>$pager]);
    }
    public function actionAdd(){
        $goods=new Goods();
        $content=new GoodsIntro();
        $request=\Yii::$app->request;
        if($request->isPost){
            $goods->load($request->post());
            $content->load($request->post());
            if($goods->validate()&&$content->validate()){
                $count=GoodsDayCount::find()->where(['=','day',date('Ymd')])->one();
                if(!$count){
                    $count=new GoodsDayCount();
                    $count->day=date('Ymd');
                    $count->count=1;
                    $count->save();
                    $sn=date('Ymd').sprintf('%04d',1);

                }else{
                    $count->count+=1;
                    $count->save();
                    $sn=date('Ymd').sprintf('%04d',$count->count);
                }
                $goods->sn=$sn;
                $goods->create_time=time();
                $goods->save();
                $content->goods_id=$goods->id;
                $content->save();
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['index']);
            }
        }


        return $this->render('add',['goods'=>$goods,'content'=>$content]);
    }
    public function actionEdit($id){
        $goods=Goods::find()->where(['id'=>$id])->one();
        $content=GoodsIntro::find()->where(['goods_id'=>$id])->one();
        $request=\Yii::$app->request;
        if($request->isPost){
            $goods->load($request->post());
            $content->load($request->post());
            if($goods->validate() && $content->validate()){
                $goods->create_time=time();
                $goods->save();
                $content->save();
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['index']);
            }
        }
        return $this->render('add',['goods'=>$goods,'content'=>$content]);
    }
    public function actionDelete($id){
        $goods=Goods::find()->where(['id'=>$id])->one();
        $goods->delete();
        \Yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['index']);
    }
    public function actionGallery($id){
        $pics=GoodsGallery::find()->where(['goods_id'=>$id])->all();
        return $this->render('gallery',['pics'=>$pics]);
    }
    public function actionGdel($id){

        $pic=GoodsGallery::find()->where(['id'=>$id])->one();
        $pic->delete();
        \Yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['goods/gallery?id='.$pic->goods_id]);
    }
    public function actionAddgallery($id){
            $model= new GoodsGallery();
            $request=\Yii::$app->request;
            if($request->isPost){
                $model->load($request->post());
                $model->goods_id=$id;
                $model->save();

            }
            return $this->render('addgallery',['model'=>$model]);
    }
    public function actions() {
        return [
            's-upload' => [
                'class' => UploadAction::className(),
                'basePath' => '@webroot/upload',
                'baseUrl' => '@web/upload',
                'enableCsrf' => true, // default
                'postFieldName' => 'Filedata', // default
                //BEGIN METHOD
                //'format' => [$this, 'methodName'],
                //END METHOD
                //BEGIN CLOSURE BY-HASH
                'overwriteIfExist' => true,
//                'format' => function (UploadAction $action) {
//                    $fileext = $action->uploadfile->getExtension();
//                    $filename = sha1_file($action->uploadfile->tempName);
//                    return "{$filename}.{$fileext}";
//                },
                //END CLOSURE BY-HASH
                //BEGIN CLOSURE BY TIME
                'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filehash = sha1(uniqid() . time());
                    $p1 = substr($filehash, 0, 2);
                    $p2 = substr($filehash, 2, 2);
                    return "{$p1}/{$p2}/{$filehash}.{$fileext}";
                },
                //END CLOSURE BY TIME
                'validateOptions' => [
                    'extensions' => ['jpg', 'png','bmp','gif'],
                    'maxSize' => 1 * 1024 * 1024, //file size
                ],
                'beforeValidate' => function (UploadAction $action) {
                    //throw new Exception('test error');
                },
                'afterValidate' => function (UploadAction $action) {},
                'beforeSave' => function (UploadAction $action) {},
                'afterSave' => function (UploadAction $action) {

//                    $action->getFilename(); // "image/yyyymmddtimerand.jpg"
//                    $action->getWebUrl(); //  "baseUrl + filename, /upload/image/yyyymmddtimerand.jpg"
//                    $action->getSavePath(); // "/var/www/htdocs/upload/image/yyyymmddtimerand.jpg"

                    $qiniu = new Qiniu(\Yii::$app->params['qiniuyun']);
                    $key = $action->getWebUrl();
                    $file=$action->getSavePath();
                    $qiniu->uploadFile($file,$key);
                    $url = $qiniu->getLink($key);
                    $action->output['fileUrl'] = $url;
                },
            ],
        ];
    }


}
