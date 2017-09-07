<?php

namespace backend\controllers;

use backend\models\Brand;
use yii\web\Request;
use yii\web\UploadedFile;

class BrandController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $models=Brand::find()->all();
        return $this->render('index',['models'=>$models]);
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
                return $this->redirect('brand/index');
            }


        }


        return $this->render('add',['model'=>$model]);

    }

}
