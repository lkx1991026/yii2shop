<?php

namespace backend\controllers;

use backend\models\GoodsCategory;

class GoodsCategoryController extends \yii\web\Controller
{
    public function actionAdd(){
        $model=new GoodsCategory();
        $requset=\Yii::$app->request;
        if($requset->isPost){
            $model->load($requset->post());
            if($model->validate()){
                if($model->parent_id){
                    $parent=GoodsCategory::findOne(['id'=>$model->parent_id]);
                    $model->appendTo($parent);
                }else{
                    $model->makeRoot();
                }
            }
            \Yii::$app->session->setFlash('success','添加成功');
            return $this->redirect(['index']);
        }
        return $this->render('add',['model'=>$model]);
    }
    public function actionIndex()
    {   $arr=GoodsCategory::find()->asArray()->all();

        $nodes=GoodsCategory::getchildren($arr);
        return $this->render('index',['nodes'=>$nodes]);

    }
    public function actionTest(){
        return $this->renderPartial('ztree');
    }
    public function actionEdit($id){
        $model=GoodsCategory::find()->select(['id','parent_id','name','intro'])->where(['id'=>$id])->one();
        $requset=\Yii::$app->request;
        if($requset->isPost){
            $model->load($requset->post());
            if($model->validate()){
                if($model->parent_id){
                    $parent=GoodsCategory::findOne(['id'=>$model->parent_id]);
                    $model->appendTo($parent);
                }else{
                    $model->makeRoot();
                }
            }
            \Yii::$app->session->setFlash('success','添加成功');
            return $this->redirect(['index']);
        }

        return $this->render('add',['model'=>$model]);
    }
    public function actionDelete($id){
        $node=GoodsCategory::findOne(['id'=>$id]);
        $children=GoodsCategory::findAll(['parent_id'=>$node->id]);
//        var_dump($children);exit;
        if($children!=null){
            \Yii::$app->session->setFlash('success','删除错误');

        }else{
            \Yii::$app->session->setFlash('success','删除成功');
            $node->delete();
        }
        return $this->redirect('index');
    }
}