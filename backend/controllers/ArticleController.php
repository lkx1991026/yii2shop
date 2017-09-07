<?php

namespace backend\controllers;

use backend\models\Article;
use backend\models\ArticleCategory;
use backend\models\ArticleDetail;
use yii\behaviors\TimestampBehavior;

class ArticleController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $models=Article::find()->all();
        return $this->render('index',['models'=>$models]);
    }
    public function actionAdd(){
            $model=new Article();
            $request=\Yii::$app->request;
            if($request->isPost){
                $model->load($request->post());
                if($model->validate()){
                    $model->save();
                    $id=\Yii::$app->db->getLastInsertID();
                    $content=new ArticleDetail();
                    $content->article_id=$id;
                    $content->content=$model->content;
                    $content->save();
                    \Yii::$app->session->setFlash('success','添加成功');
                    return $this->redirect(['article/index']);
                }
            }
            $categorys=ArticleCategory::find()->all();
            return $this->render('add',['model'=>$model,'categorys'=>$categorys]);
    }
    public function actionShow($id){
//        $article
    }
    public function actionEdit($id){
        $model=Article::find()->where(['id'=>$id])->one();
        $model->content=$model->articleDetail->content;
        $request=\Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $model->save();
                $content=ArticleDetail::find()->where(['article_id'=>$id])->one();
                $content->content=$model->content;
                $content->save();
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['article/index']);
            }
        }
        $categorys=ArticleCategory::find()->all();
        return $this->render('add',['model'=>$model,'categorys'=>$categorys]);
    }
    public function actionDelete($id){
        $model=Article::find()->where(['id'=>$id])->one();
        $model->status=-1;
        var_dump($model->getErrors());
        exit;
        \Yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['article/index']);
    }
}
