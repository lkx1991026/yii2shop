<?php
namespace frontend\controllers;


use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsGallery;
use frontend\models\Address;
use frontend\models\Cart;
use frontend\models\Order;
use frontend\models\OrderGoods;
use yii\db\Exception;
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
            $list=GoodsCategory::find()->select(['name','id','parent_id'])->asArray()->all();
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
            return $this->renderPartial('list',['goods'=>$goods,'item'=>$item,'father_items'=>$father_items,'prevpage'=>$prevPage,'nextpage'=>$nextPage,'pages'=>$pages,'page'=>$page,'list'=>$list]);
        }
        return $this->renderPartial('list',['goods'=>$goods,'item'=>$item,'prevpage'=>$prevPage,'nextpage'=>$nextPage,'pages'=>$pages,'page'=>$page,'list'=>$list]);
    }
    public function actionDetail($id){
        $list=GoodsCategory::find()->select(['name','id','parent_id'])->asArray()->all();
        $model=Goods::find()->where('id='.$id)->one();
        $parent=GoodsCategory::find()->where(['id'=>$model->goods_catgory_id])->one();
        $gfather=GoodsCategory::find()->where(['id'=>$parent->parent_id])->one();
        $ggfather=GoodsCategory::find()->where(['id'=>$gfather->parent_id])->one();
//        $count=GoodsGallery::find()->where('goods_id='.$id)->count();
        $gallerys=GoodsGallery::find()->where('goods_id='.$id)->all();
        return $this->renderPartial('show',['model'=>$model,'gallerys'=>$gallerys,'parent'=>$parent,'gfather'=>$gfather,'ggfather'=>$ggfather,'list'=>$list]);
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
    public function actionOrder(){
        if(\Yii::$app->user->isGuest){
            return $this->redirect(['member/login']);
        }else{
        $payment=Order::$payment;
        $deliveries=Order::$deliveries;
        $address=Address::findAll(['user_id'=>\Yii::$app->user->id]);
        $carts=Cart::findAll(['member_id'=>\Yii::$app->user->id]);
        $goods=[];
        foreach($carts as $cart){
            $model=Goods::find()->where(['id'=>$cart->goods_id])->asArray()->one();
            $model['amount']=$cart->amount;
            $goods[]=$model;
        }
        $request=\Yii::$app->request;
        if($request->isPost){
            $order=new Order();
            $order->load($request->post(),'');
            $address_id=$request->post('address_id');
            $model=Address::find()->where(['id'=>$address_id])->andWhere(['user_id'=>\Yii::$app->user->id])->one();
            //给地址字段赋值
            $order->address=$model->addr;
            $order->province=$model->province->name;
            $order->city=$model->city->name;
            $order->area=$model->area->name;
            $order->tel=$model->tel;
            $order->name=$model->username;
            //给配送方式赋值
            $delivery=Order::$deliveries[$order->delivery_id];
            $order->delivery_name=$delivery[0];
            $order->delivery_price=$delivery[1];
            //给支付方式赋值
            $payment=Order::$payment[$order->payment_id];
            $order->payment_name=$payment[0];
            $order->member_id=\Yii::$app->user->id;
            $order->status=1;
            $order->total=0;
            $order->create_time=time();
            $trasaction=\Yii::$app->db->beginTransaction();
            try {
                $order->save();
                $carts = Cart::find()->where(['member_id' => \Yii::$app->user->id])->all();
                foreach ($carts as $cart) {
                    $order_goods = new OrderGoods();

                    $goods = Goods::find()->where(['id' => $cart->goods_id])->one();
                    if ($cart->amount > $goods->stock) {
                        echo '库存不足,请修改订单信息';
                        throw new Exception('库存不足,请修改订单信息');

                    }
                    $order_goods->order_id = $order->id;
                    $order_goods->goods_id = $goods->id;
                    $order_goods->goods_name = $goods->name;
                    $order_goods->logo = substr($goods->logo,0,4)=='http'?$goods->logo:str_replace('/upload/', 'http://admin.yii2shop.com/upload/', $goods->logo);
                    $order_goods->price = $goods->shop_price;
                    $order_goods->amount = $cart->amount;
                    $goods->stock-=$cart->amount;
                    $goods->save();
                    $order_goods->total = $cart->amount * $goods->shop_price;
                    $order->total += $order_goods->total;
                    $order_goods->order_id=$order->id;
                    $order->save();
                    $order_goods->save();
                    $cart->delete();
//                    var_dump($order_goods);exit;
                }

                $trasaction->commit();
            }catch(Exception $e){
                $trasaction->rollBack();exit;
            }

            return $this->redirect(['index/index']);
        }

            return $this->renderPartial('order',['payment'=>$payment,'deliveries'=>$deliveries,'address'=>$address,'goods'=>$goods]);
        }
    }
    public function actionMyorder(){
        if(\Yii::$app->user->isGuest){
            return $this->redirect(['member/login']);
        }
        $orderlists=[];
        $member_id=\Yii::$app->user->id;
//        var_dump($member_id);
        $orders=Order::find()->where(['member_id'=>$member_id])->orderBy(['create_time'=>'desc'])->all();
//        var_dump($orders);exit;
        foreach($orders as $order){
            $goods=OrderGoods::find()->where(['order_id'=>$order->id])->asArray()->all();
            foreach($goods as $good){
                $good['username']=$order->name;
                $good['payment']=$order->payment_name;
                $good['create_time']=$order->create_time;
                $good['status']=$order->status;
                $orderlists[$good['order_id']][]=$good;
            }

        }
//        var_dump($orderlists);exit;
        return $this->renderPartial('myorder',['orderlists'=>$orderlists]);
    }
}