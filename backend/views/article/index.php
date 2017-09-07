<?php
/* @var $this yii\web\View */
?>
<h1>文章列表</h1>

<table class="table table-bordered table-responsive">
    <tr>
        <td>id</td>
        <td>文章名</td>
        <td>简介</td>
        <td>状态</td>
        <td>文章分类</td>
        <td>创建时间</td>
        <td>操作</td>
    </tr>
    <?php foreach($models as $model):?>
        <tr>
            <td><?=$model->id?></td>
            <td><?=$model->name?></td>
            <td><?=$model->intro?></td>
            <td><?=$model->status?'正常':'隐藏'?></td>
            <td><?=$model->articleCategory->name?></td>
            <td><?=date('Y-m-d H:i:s',$model->create_time)?></td>
            <td>
                <a href="<?=\yii\helpers\Url::to(['/article/show?id='.$model->id])?>" class="btn btn-info btn-sm">查看</a>
                <a href="<?=\yii\helpers\Url::to(['/article/delete?id='.$model->id])?>" class="btn btn-warning btn-sm">删除</a>
                <a href="<?=\yii\helpers\Url::to(['/article/edit?id='.$model->id])?>" class="btn btn-danger btn-sm">修改</a>
            </td>
        </tr>
    <?php endforeach;?>
</table>