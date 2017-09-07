<?php
/* @var $this yii\web\View */
?>
<h1>品牌列表</h1>

<table class="table table-bordered table-responsive">
    <tr>
        <td>id</td>
        <td>品牌名</td>
        <td>状态</td>
        <td>简介</td>
        <td>logo</td>
        <td>操作</td>
    </tr>
    <?php foreach($models as $model):?>
        <tr>
            <td><?=$model->id?></td>
            <td><?=$model->name?></td>
            <td><?=$model->status?'正常':'隐藏'?></td>
            <td><?=$model->intro?></td>
            <td><img src="<?=$model->logo?>" class="img-responsive" style="width:50px;"/></td>
            <td>
                <a href="<?=\yii\helpers\Url::to('/brand/edit?id='.$model->id)?>" class="btn btn-info">编辑</a>
                <a href="<?=\yii\helpers\Url::to('/brand/delete?id='.$model->id)?>" class="btn btn-danger">删除</a>
            </td>
        </tr>
    <?php endforeach;?>
</table>
<?php echo \yii\widgets\LinkPager::widget(
        [
                'pagination'=>$pager,
                'prevPageLabel'=>'上一页',
                'nextPageLabel'=>'下一页',

        ]
)?>