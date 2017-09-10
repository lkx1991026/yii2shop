<?php
/* @var $this yii\web\View */
?>
<a href="<?=\yii\helpers\Url::to(['goods-category/add'])?>" class="btn btn-primary">添加商品分类</a>
<table class="table table-bordered table-responsive">
    <tr>
        <td>id</td>
        <td>分类名</td>
        <td>操作</td>
    </tr>
    <?php foreach($nodes as $node):?>
        <tr>
            <td><?=$node['id']?></td>
            <td><?=$node['level']?></td>
            <td>
                <a href="<?=\yii\helpers\Url::to(['goods-category/edit?id='.$node['id']])?>" class="btn btn-sm btn-info">编辑</a>
                <a href="<?=\yii\helpers\Url::to(['goods-category/delete?id='.$node['id']])?>" class="btn btn-sm btn-warning">删除</a>
            </td>
        </tr>
    <?php endforeach;?>
</table>