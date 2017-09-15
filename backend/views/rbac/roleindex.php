<?php
/**
 * @var $this \yii\web\View;
 */
?>
    <a href="<?=\yii\helpers\Url::to(['rbac/add-role'])?>" class="btn btn-info">添加角色</a>
    <table id="table_id_example" class="display table table-responsive table-bordered">
        <thead>
        <tr>
            <th>角色名称</th>
            <th>描述</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($roles as $role):?>
            <tr>
                <td><?=$role->name?></td>
                <td><?=$role->description?></td>
                <td>
                    <a href="<?=\yii\helpers\Url::to(['rbac/edit-role?name='.$role->name])?>" class="btn btn-warning btn-sm">修改</a>
                    <a href="<?=\yii\helpers\Url::to(['rbac/delete-role?name='.$role->name])?>" class="btn btn-danger btn-sm">删除</a>
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