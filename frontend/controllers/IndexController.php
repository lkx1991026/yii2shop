<?php
namespace frontend\controllers;


use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsGallery;
use yii\web\Controller;

class IndexController extends Controller{
    public function actionIndex(){
        $list=GoodsCategory::find()->select(['name','id','parent_id'])->asArray()->all();
//        $list=GoodsCategory::getList($arr);
        return $this->renderPartial('index',['list'=>$list]);
    }
    public function actionShow($id,$page=1){
            $item=GoodsCategory::find()->where(['id'=>$id])->one();


            $arr=GoodsCategory::find()->select(['id'])->where(['>=','lft',$item->lft])->andWhere(['<=','rgt',$item->rgt])->andWhere(['=','tree',$item->tree])->andWhere(['=','depth',2])->asArray()->all();

            $goods=[];
            foreach($arr as $v){
//                var_dump($v['id']);
                $good=Goods::find()->where(['goods_catgory_id'=>$v['id']])->asArray()->all();
                foreach($good as $goo){
                    $goods[]=$goo;
                }
            }
            $count=count($goods);

            $pagesize=4;
            $pages=ceil($count/$pagesize);
            $page=($page>=$pages)?$pages:$page;
            $page=($page<0)?1:$page;
            $start=($page-1)*$pagesize;
            $goods=array_slice($goods,$start,$pagesize);
            $prevPage=$page<=1?1:$page-1;
            $nextPage=$page>=$pages?$pages:$page+1;

        if($item->depth!=0){
            $father_items=$item->parents()->all();
            return $this->renderPartial('list',['goods'=>$goods,'item'=>$item,'father_items'=>$father_items,'prevpage'=>$prevPage,'nextpage'=>$nextPage,'pages'=>$pages,'page'=>$page]);
        }
        return $this->renderPartial('list',['goods'=>$goods,'item'=>$item,'prevpage'=>$prevPage,'nextpage'=>$nextPage,'pages'=>$pages,'page'=>$page]);
    }
    public function actionDetail($id){
        $model=Goods::find()->where('id='.$id)->one();
        $parent=GoodsCategory::find()->where(['id'=>$model->goods_catgory_id])->one();
        $gfather=GoodsCategory::find()->where(['id'=>$parent->parent_id])->one();
        $ggfather=GoodsCategory::find()->where(['id'=>$gfather->parent_id])->one();
//        $count=GoodsGallery::find()->where('goods_id='.$id)->count();
        $gallerys=GoodsGallery::find()->where('goods_id='.$id)->all();
        return $this->renderPartial('show',['model'=>$model,'gallerys'=>$gallerys,'parent'=>$parent,'gfather'=>$gfather,'ggfather'=>$ggfather]);
    }
}