<?php

namespace backend\controllers;

use backend\models\Article;
use backend\models\ArticleCategory;
use backend\models\ArticleDetail;
use yii\behaviors\TimestampBehavior;
use yii\data\Pagination;

class ArticleController extends \yii\web\Controller
{
    public function actionIndex()
    {   $count=Article::find()->where(['!=','status',-1])->count();
        $pager=new Pagination(
            [
                'defaultPageSize'=>4,
                'totalCount'=>$count
            ]
        );
        $models=Article::find()->where(['!=','status',-1])->limit($pager->limit)->offset($pager->offset)->all();
        return $this->render('index',['models'=>$models,'pager'=>$pager]);
    }
    public function actionAdd(){
            $model=new Article();
            $content=new ArticleDetail();
            $request=\Yii::$app->request;
            if($request->isPost){
                $model->load($request->post());
                $content->load($request->post());
                if($model->validate()){
                    $model->save();
                    $id=\Yii::$app->db->getLastInsertID();
                    $content->article_id=$id;
                    $content->save();
                    \Yii::$app->session->setFlash('success','添加成功');
                    return $this->redirect(['article/index']);
                }
            }
            $categorys=ArticleCategory::find()->all();
            return $this->render('add',['model'=>$model,'categorys'=>$categorys,'content'=>$content]);
    }
    public function actionShow($id){
    }
    public function actionEdit($id){
        $model=Article::find()->where(['id'=>$id])->one();
        $content=ArticleDetail::find()->where(['Article_id'=>$id])->one();
        $request=\Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            $content->load($request->post());
            if($model->validate()){
                $model->save();

                $content->save();
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['article/index']);
            }
        }
        $categorys=ArticleCategory::find()->all();
        return $this->render('add',['model'=>$model,'categorys'=>$categorys,'content'=>$content]);
    }
    public function actionDelete($id){
        $model=Article::find()->where(['id'=>$id])->one();
        $model->status=-1;
        $model->save();
        \Yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['article/index']);
    }
}
