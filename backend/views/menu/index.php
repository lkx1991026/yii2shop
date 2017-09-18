<?php
?>
<table class="table table-bordered table-responsive">
    <tr>
        <th>菜单名称</th>
        <th>菜单路由</th>
        <th>菜单操作</th>
    </tr>
    <?php foreach($lists as $list):?>
        <tr>
            <td><?=$list['name']?></td>
            <td><?=$list['link']?$list['link']:''?></td>
            <td>
                <a href="<?=\yii\helpers\Url::to(['menu/edit?id='.$list['id']])?>" class="btn btn-info">编辑</a>
                <a href="<?=\yii\helpers\Url::to(['menu/delete?id='.$list['id']])?>" class="btn btn-warning">删除</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
<?php //echo \yii\widgets\LinkPager::widget([
//        'pagination'=>$pager
//]);?>
