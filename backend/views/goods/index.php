<?php
/* @var $this yii\web\View */
?>
<table class="table table-responsive table-bordered">
    <tr>
        <td>id</td>
        <td>商品名称</td>
        <td>商品货号</td>
        <td>商品图片</td>
        <td>商品分类</td>
        <td>商品品牌</td>
        <td>市场价格</td>
        <td>市场售价</td>
        <td>库存</td>
        <td>是否上架</td>
        <td>状态</td>
        <td>添加时间</td>
        <td>操作</td>
    </tr>
    <?php foreach($goods as $v):?>
        <tr>
            <td><?=$v->id?></td>
            <td><?=$v->name?></td>
            <td><?=$v->sn?></td>
            <td><img src="<?=$v->logo?>" style="width:50px;"></td>
            <td><?=$v->cat->name?></td>
            <td><?=$v->brand->name?></td>
            <td><?=$v->market_price?></td>
            <td><?=$v->shop_price?></td>
            <td><?=$v->stock?></td>
            <td><?=$v->is_on_sale?'是':'否'?></td>
            <td><?=$v->status?'正常':'回收站'?></td>
            <td><?=date('Y-m-d H:i:s',$v->create_time)?></td>
            <td>
                <a href="<?=\yii\helpers\Url::to(['gallery?id='.$v->id])?>" class="btn btn-primary btn-sm">相册</a>
                <a href="<?=\yii\helpers\Url::to(['edit?id='.$v->id])?>" class="btn btn-info btn-sm">编辑</a>
                <a href="<?=\yii\helpers\Url::to(['delete?id='.$v->id])?>" class="btn btn-warning btn-sm">删除</a>
                <a href="<?=\yii\helpers\Url::to(['show?id='.$v->id])?>" class="btn btn-primary btn-sm">查看</a>
            </td>
        </tr>
    <?php endforeach;?>
</table>
<?php echo \yii\widgets\LinkPager::widget(
        [
                'pagination'=>$pager,
                'nextPageLabel'=>'下一页',
                'prevPageLabel'=>'上一页'
        ]
);
?>
