<?php

namespace backend\controllers;

use backend\models\Brand;
use yii\data\Pagination;
use yii\web\Request;
use yii\web\UploadedFile;

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
            $model->file=UploadedFile::getInstance($model,'file');
            if($model->validate()) {
                if ($model->file) {
                    $file = '/upload/' . uniqid() . '.' . $model->file->getExtension();
                    $model->file->saveAs(\Yii::getAlias('@webroot') . $file, false);
                    $model->logo = $file;
                }
                $model->save(false);
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
            $model->file=UploadedFile::getInstance($model,'file');
            if($model->validate()) {
                if ($model->file) {
                    $file = '/upload/' . uniqid() . '.' . $model->file->getExtension();
                    $model->file->saveAs(\Yii::getAlias('@webroot') . $file, false);
                    $model->logo = $file;
                }
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

}
