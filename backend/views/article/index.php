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
            <td><?=$model->id ?></td>
            <td><?=$model->name?></td>
            <td><?=$model->intro?></td>
            <td><?=$model->status?'正常':'隐藏'?></td>
            <td><?=$model->articleCategory->name?></td>
            <td><?=date('Y-m-d H:i:s',$model->create_time)?></td>
            <td>
                <a href="<?=\yii\helpers\Url::to(['/article/show?id='.$model->id])?>" class="btn btn-info btn-sm ">查看</a>
                <a href="<?=\yii\helpers\Url::to(['/article/edit?id='.$model->id])?>" class="btn btn-warning btn-sm delete">修改</a>
                <a href="javascript:;" class="btn btn-danger btn-sm delete">删除</a>
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
);
$del_url=\yii\helpers\Url::to(['article/delete']);
$this->registerJs(new \yii\web\JsExpression(
    <<<js
        $('.delete').on('click',function () {
        var tr=$(this).closest('tr');
        var id= tr.find('td:first').text();
        if(confirm('确认删除么?')){
             $.post("{$del_url}",{id:id},function (data) {
            var data=JSON.parse(data);
            if(data.success){
                alert('删除成功');
                tr.hide('slow');
            }
        })       
        }   
    })


js

))
?>