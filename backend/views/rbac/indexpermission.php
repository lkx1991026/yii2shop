<?php
/**
 * @var $this \yii\web\View;
 */
?>
<a href="<?=\yii\helpers\Url::to(['rbac/add-permission'])?>" class="btn btn-info">添加权限</a>
<table id="table_id_example" class="display table table-responsive table-bordered">
    <thead>
        <tr>
            <th>权限名称(路由)</th>
            <th>描述</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($permissions as $permission):?>
            <tr>
                <td><?=$permission->name?></td>
                <td><?=$permission->description?></td>
                <td>
                    <a href="<?=\yii\helpers\Url::to(['rbac/edit-permission?name='.$permission->name])?>" class="btn btn-warning btn-sm">修改</a>
                    <a href="<?=\yii\helpers\Url::to(['rbac/delete-permission?name='.$permission->name])?>" class="btn btn-danger btn-sm">删除</a>
                </td>
            </tr>
        <?php endforeach;?>
    </tbody>
</table>
<?php
$this->registerCssFile(Yii::getAlias('@web/datatable/css/jquery.dataTables.css'));
$this->registerJsFile('@web/datatable/js/jquery.dataTables.js',['depends'=>\yii\web\JqueryAsset::className()]);
$this->registerJs(new \yii\web\JsExpression(
        <<<JS
        $(document).ready( function () {
        $('#table_id_example').DataTable();
    } );
JS

    ));