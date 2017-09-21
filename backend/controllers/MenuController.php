<?php
namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\Menu;
use yii\data\Pagination;
use yii\web\Controller;

class MenuController extends Controller{
    public function actionAdd(){
        $model=new Menu();
        $model->scenario=Menu::SCENARIO_ADD;
        $request=\Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                if($model->parent_id==0){
                    $model->link='';
                }
                $model->save();
                \Yii::$app->session->setFlash('success','添加成功');
//                return $this->redirect(['menu/index']);
            }
            }
        return $this->render('add',['model'=>$model]);
    }
    public function actionIndex(){
        $query=Menu::find();
        $model=$query->asArray()->all();
        $lists=Menu::getListByCat($model,0,0);
        return $this->render('index',['lists'=>$lists]);
    }
    public function actionEdit($id){
        $model=Menu::findOne(['id'=>$id]);
        $model->scenario=Menu::SCENARIO_EDIT;
        $request=\Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                if($model->parent_id==0){
                    $model->link='';
                }
                $model->save();
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['menu/index']);
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    public function actionDelete($id){
        if(Menu::findOne(['parent_id'=>$id])){
            \Yii::$app->session->setFlash('success','该菜单下有子菜单,不能删除!');
            return $this->redirect(['menu/index']);
        }
        $model=Menu::findOne(['id'=>$id]);
        $model->delete();
        \Yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['menu/index']);
    }
    public function behaviors()
    {
        return [
            'filter'=>[
                'class'=>RbacFilter::className(),
                'except'=>['login','logout','error','captcha']
            ]
        ];
    }
}