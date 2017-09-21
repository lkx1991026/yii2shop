<?php
namespace frontend\controllers;


use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsGallery;
use frontend\models\Cart;
use yii\web\Controller;
use yii\web\Cookie;

class IndexController extends Controller{
    public $enableCsrfValidation=false;
    public function actionIndex(){
        $list=GoodsCategory::find()->select(['name','id','parent_id'])->asArray()->all();
//        $list=GoodsCategory::getList($arr);
        return $this->renderPartial('index',['list'=>$list]);
    }
    public function actionShow($id,$page=1){
            $item=GoodsCategory::find()->where(['id'=>$id])->one();//查询当前id对用的分类对象
            //利用一条sql语句查询该分类下面是否还有叶子分类,如果有就把叶子分类保存到一个数组中
            $arr=GoodsCategory::find()->select(['id'])->where(['>=','lft',$item->lft])->andWhere(['<=','rgt',$item->rgt])->andWhere(['=','tree',$item->tree])->andWhere(['=','depth',2])->asArray()->all();

            $goods=[];
            foreach($arr as $v){
//                var_dump($v['id']);
                //循环叶子ID获得对应的商品
                $good=Goods::find()->where(['goods_catgory_id'=>$v['id']])->asArray()->all();
                foreach($good as $goo){
                    //进一步循环保证$goods中数据的统一性;
                    $goods[]=$goo;
                }
            }
            //准备分页所需的数据
            $count=count($goods);
            $pagesize=4;
            $pages=ceil($count/$pagesize);
            $page=($page>=$pages)?$pages:$page;
            $page=($page<0)?1:$page;
            $start=($page-1)*$pagesize;
            $goods=array_slice($goods,$start,$pagesize);
            $prevPage=$page<=1?1:$page-1;
            $nextPage=$page>=$pages?$pages:$page+1;
        //判断分类是否为顶级分类,选择性将数据传到视图(生成路径信息)
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
    public function actionAddcart($goods_id,$amount){
        if(\Yii::$app->user->isGuest){
            $cookies=\Yii::$app->request->cookies;
            $value=$cookies->getValue('cart');
            if($value){
                $carts=unserialize($value);
            }else{
                $carts=[];
            }
            if(array_key_exists($goods_id,$carts)){
                $carts[$goods_id] += $amount;
            }else{
                $carts[$goods_id] = $amount;
            }
            $carts=serialize($carts);
            $cookies=\Yii::$app->response->cookies;
            $cookie=new Cookie();
            $cookie->name='cart';
            $cookie->value=$carts;
            $cookie->expire=time()+24*3600*7;
            $cookies->add($cookie);

        }else{
            //登陆用户
            $model=Cart::find()->where(['=','goods_id',$goods_id])->andWhere(['=','member_id',\Yii::$app->user->getId()])->one();
            if($model){
                $model->amount+=$amount;
            }else{
                $model=new Cart();
                $model->goods_id=$goods_id;
                $model->member_id=\Yii::$app->user->getId();
                $model->amount=$amount;
            }
            $model->save();
        }
            return $this->redirect(['index/cart']);
    }
    public function actionCart(){
        if(\Yii::$app->user->isGuest){
            //游客
            $cookie=\Yii::$app->request->cookies;//获取浏览器上的cookie
            $value=$cookie->getValue('cart');//获取对应的购物车数据
            if($value){
                $carts=unserialize($value);//反序列化
            }else{
                $carts=[];
            }
            $model=Goods::find()->where(['in','id',array_keys($carts)])->asArray()->all();//获得商品数据
            $amount=0;//初始化所有商品总价0
            foreach($model as $good){
                $amount+=$good['shop_price']*$carts[$good['id']];//循环得到商品总价
            }
            return $this->renderPartial('cart',['model'=>$model,'carts'=>$carts,'amount'=>$amount]);
        }else{
            //登录用户
            $goods_ids=Cart::find()->select(['goods_id','amount'])->where(['=','member_id',\Yii::$app->user->getId()])->asArray()->all();
            $carts=[];
            foreach($goods_ids as $goods_id){
                $carts[$goods_id['goods_id']]=$goods_id['amount'];
            }
            $model=Goods::find()->where(['in','id',array_keys($carts)])->asArray()->all();
            $amount=0;
            foreach($model as $good){
                $amount+=$good['shop_price']*$carts[$good['id']];
            }
            return $this->renderPartial('cart',['model'=>$model,'carts'=>$carts,'amount'=>$amount]);
        }


    }
    public function actionChangeamount(){
        $goods_id=\Yii::$app->request->post('goods_id');
        $amount=\Yii::$app->request->post('amount');
        if(\Yii::$app->user->isGuest){
            $cookies=\Yii::$app->request->cookies;
            $value=$cookies->getValue('cart');
            $carts=unserialize($value);
            if($amount==0){
                unset($carts[$goods_id]);
            }else{
                $carts[$goods_id]=$amount;
            }
            $cookies=\Yii::$app->response->cookies;
            $cookie= new Cookie();
            $cookie->name='cart';
            $cookie->value=serialize($carts);
            $cookie->expire=time()+86400;
            $cookies->add($cookie);
        }else{
            //登陆用户
            $model=Cart::find()->where(['=','goods_id',$goods_id])->andWhere(['=','member_id',\Yii::$app->user->getId()])->one();
            if($amount==0){
                $model->delete();
            }else{
                $model->amount=$amount;
                $model->save();
            }


        }

    }
}