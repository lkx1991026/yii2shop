<?php

namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\Goods;
use backend\models\GoodsDayCount;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use backend\models\Search;
use flyok666\qiniu\Qiniu;
use flyok666\uploadifive\UploadAction;
use yii\data\Pagination;

class GoodsController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $model=new Search();
        $request=\Yii::$app->request;
        if($request->isGet){
        $model->load($request->get());
        $query=Search::getQuery($model);
    }
        $count=$query->count();

        $pager=new Pagination(
            [
                'defaultPageSize'=>4,
                'totalCount'=>$count
            ]
        );
        $goods=$query->limit($pager->limit)->offset($pager->offset)->all();
        return $this->render('index',['goods'=>$goods,'pager'=>$pager,'model'=>$model]);
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
        $goods->status=0;
        $goods->save();
        \Yii::$app->session->setFlash('success','删除成功,可在回收站找回!');
        return $this->redirect(['index']);
    }
    public function actionGallery($id){
        $pics=GoodsGallery::find()->where(['goods_id'=>$id])->all();
        return $this->render('gallery',['pics'=>$pics,'goods_id'=>$id]);
    }
    public function actionGdel(){
        $id=\Yii::$app->request->post('id');
        $pic=GoodsGallery::find()->where(['id'=>$id])->one();
        $pic->delete();
//        \Yii::$app->session->setFlash('success','删除成功');
//        return $this->redirect(['goods/gallery?id='.$pic->goods_id]);
    }
    public function actionRecycle(){
        $recycles=Goods::find()->where('status=0')->asArray()->all();
        echo json_encode($recycles);
    }
    public function actionRecovery(){
        $id=$_POST['id'];
        $model=Goods::find()->where('id='.$id)->one();
        if(!$model){
            echo json_encode(
                [
                    'success'=>false,
                    'msg'=>'恢复失败,数据不存在'
                ]
            );
        }else{
            $model->status=1;
            $model->save();
            echo json_encode(
                [
                    'success'=>true,
                    'msg'=>'数据恢复成功'
                ]
            );
        }
    }
    public function actionShow($id){
        $model=Goods::find()->where('id='.$id)->one();
        $count=GoodsGallery::find()->where('goods_id='.$id)->count();
        $gallerys=GoodsGallery::find()->where('goods_id='.$id)->all();
        return $this->render('show',['model'=>$model,'gallerys'=>$gallerys,'count'=>$count]);

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

//                    $qiniu = new Qiniu(\Yii::$app->params['qiniuyun']);
                    $key = $action->getWebUrl();
//                    $file=$action->getSavePath();
//                    $qiniu->uploadFile($file,$key);
//                    $url = $qiniu->getLink($key);

                    $model=new GoodsGallery();
                    $model->goods_id=$_REQUEST['goods_id'];
                    if($key!=null){
                        $model->path=$key;
                        $model->save();
                        $id=$model->id;
                    }

                    $action->output['fileUrl'] = $key;
                    $action->output['id'] = $id;
                },
            ],
            'x-upload' => [
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

//                    $qiniu = new Qiniu(\Yii::$app->params['qiniuyun']);
                    $key = $action->getWebUrl();
//                    $file=$action->getSavePath();
//                    $qiniu->uploadFile($file,$key);
//                    $url = $qiniu->getLink($key);
                    $action->output['fileUrl'] = $key;
                },
            ],
            'upload' => [
                'class' => 'kucha\ueditor\UEditorAction',
            ]
        ];
    }
    public function behaviors()
    {
        return [
            'filter'=>[
                'class'=>RbacFilter::className(),
                'except'=>['s-upload','x-upload']
            ]
        ];
    }

}
