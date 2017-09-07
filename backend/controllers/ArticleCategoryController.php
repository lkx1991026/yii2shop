<?php

namespace backend\controllers;

use backend\models\ArticleCategory;
use yii\data\Pagination;
use yii\web\Request;

class ArticleCategoryController extends \yii\web\Controller
{
    public function actionIndex()
    {   $count=ArticleCategory::find()->all();
        $pager=new Pagination(
            [
                'defaultPageSize'=>4,
                'totalCount'=>$count
            ]
        );
       $models=ArticleCategory::find()->limit($pager->limit)->offset($pager->offset)->where(['!=','status',-1])->all();
       return $this->render('index',['models'=>$models,'pager'=>$pager]);
    }
    public function actionAdd(){
        $model=new ArticleCategory();
        $request=new Request();
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()) {
                $model->save();
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect('/article-category/index');
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    public function actionEdit($id){
        $model=ArticleCategory::find()->where(['id'=>$id])->one();
        $request=new Request();
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()) {
                $model->save();
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect('/article-category/index');
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    public function actionDelete($id){
        $model=ArticleCategory::find()->where(['id'=>$id])->one();
        $model->status=-1;
        $model->save();
        \Yii::$app->session->setFlash('success','删除成功');
        return $this->redirect('/article-category/index');

    }

}
