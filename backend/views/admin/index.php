<?php
/* @var $this yii\web\View */
?>
<h1>管理员列表</h1>
<a href="<?=\yii\helpers\Url::to(['admin/add'])?>" class="btn btn-info glyphicon glyphicon-plus"></a>
<table class="table table-responsive table-bordered">
    <tr>
        <th>id</th>
        <th>管理员名</th>
        <th>管理员邮箱</th>
        <th>创建时间</th>
        <th>更新时间</th>
        <th>上次登录时间</th>
        <th>上次登录ip</th>
        <th>管理员状态</th>
        <th>操作</th>
    </tr>
    <?php foreach ($models as $model):?>
        <tr>
            <td><?=$model->id?></td>
            <td><?=$model->username?></td>
            <td><?=$model->email?></td>
            <td><?=date('Y-m-d H:i:s',$model->created_at)?></td>
            <td><?=$model->updated_at?date('Y-m-d H:i:s',$model->updated_at):''?></td>
            <td><?=$model->last_login_time?date('Y-m-d H:i:s',$model->last_login_time):''?></td>
            <td><?=$model->last_login_ip?></td>
            <td><?=$model->status?'正常':'禁用'?></td>
            <td>
                <a href="<?=\yii\helpers\Url::to(['/admin/edit?id='.$model->id])?>" class="btn btn-warning glyphicon glyphicon-pencil"></a>
                <a href="<?=\yii\helpers\Url::to(['/admin/delete?id='.$model->id])?>" class="btn btn-danger glyphicon glyphicon-trash"></a>
            </td>
        </tr>
        <?php endforeach;?>
</table>
