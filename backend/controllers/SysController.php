<?php
namespace backend\controllers;

use backend\models\GoodsCategory;
use frontend\controllers\IndexController;
use yii\web\Controller;


class SysController extends Controller{
    public static function actionIndexCategories()
    {
        $html = '';
        $list = GoodsCategory::find()->asArray()->all();
//        var_dump($list);exit;
        foreach($list as $v){
            if($v['parent_id']==0){

                $html.='<div class="cat">';
                $html.='<h3><a href="'.\yii\helpers\Url::to(['index/show.html','id'=>$v['id']]).'">'.$v['name'].'</a> <b></b></h3>';
                $html.='<div class="cat_detail">';
                foreach($list as $k) {
                    if ($k['parent_id'] == $v['id']) {

                        $html .= '<dl class="dl">';
                        $html .= '<dt><a href="' . \yii\helpers\Url::to(['index/show.html', 'id' => $k['id']]) . '">' . $k['name'] . '</a></dt>';
                        $html .= '<dd>';
                        foreach ($list as $i) {
                            if ($i['parent_id'] == $k['id']) {

                                $html .= '<a href="' . \yii\helpers\Url::to(['index/show.html', 'id' => $i['id']]) . '">';
                                if ($i['parent_id'] == $k['id']) {
                                    $html .= $i['name'] . '</a>';
                                }
                            }
                        }
                        $html .= '</dd></dl>';
                    }
                }
                $html.='</div></div>';
            }
        }
        $redis=new \Redis();
        $redis->connect('127.0.0.1');
        $redis->set('goodscategories',$html);
        echo '缓存更新成功';
    }
}