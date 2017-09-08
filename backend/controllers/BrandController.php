<?php

namespace backend\controllers;

use backend\models\Brand;
use yii\data\Pagination;
use yii\web\Request;
use yii\web\UploadedFile;
use flyok666\uploadifive\UploadAction;

class BrandController extends \yii\web\Controller
{
    public function actionIndex()
    {   $count=Brand::find()->where(['!=','status',-1])->count();
        $pager=new Pagination(
            [
                'defaultPageSize'=>4,
                'totalCount'=>$count
            ]
        );
        $models=Brand::find()->limit($pager->limit)->offset($pager->offset)->where(['!=','status',-1])->all();
        return $this->render('index',['models'=>$models,'pager'=>$pager]);
    }

    /**
     * @return string
     */
    public function actionAdd(){
        $model=new Brand();
        $request=new Request();
        if($request->isPost){
            $model->load($request->post());
//            $model->file=UploadedFile::getInstance($model,'file');
            if($model->validate()) {
//                if ($model->file) {
//                    $file = '/upload/' . uniqid() . '.' . $model->file->getExtension();
//                    $model->file->saveAs(\Yii::getAlias('@webroot') . $file, false);
//                    $model->logo = $file;
//                }
                $model->save();
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect('/brand/index');
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    public function actionEdit($id){
        $model=Brand::find()->where(['id'=>$id])->one();
        $request=new Request();
        if($request->isPost){
            $model->load($request->post());
//            $model->file=UploadedFile::getInstance($model,'file');
            if($model->validate()) {
//                if ($model->file) {
//                    $file = '/upload/' . uniqid() . '.' . $model->file->getExtension();
//                    $model->file->saveAs(\Yii::getAlias('@webroot') . $file, false);
//                    $model->logo = $file;
//                }
                $model->save(false);
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect('/brand/index');
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    public function actionDelete($id){
        $model=Brand::find()->where(['id'=>$id])->one();
        $model->status=-1;
        $model->save();
        echo json_encode(
            [
                'success'=>true,
            'msg'=>'删除成功'
            ]
        );
//        \Yii::$app->session->setFlash('success','删除成功!');
//        return $this->redirect(['/brand/index']);
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
                    $action->output['fileUrl'] = $action->getWebUrl();
//                    $action->getFilename(); // "image/yyyymmddtimerand.jpg"
//                    $action->getWebUrl(); //  "baseUrl + filename, /upload/image/yyyymmddtimerand.jpg"
//                    $action->getSavePath(); // "/var/www/htdocs/upload/image/yyyymmddtimerand.jpg"
                },
            ],
        ];
    }
}
