<?php
/* @var $this yii\web\View */
?>

<h1>文章分类列表</h1>
<a href="<?=\yii\helpers\Url::to(['article-category/add'])?>" class="btn btn-info">添加文章分类</a>
<!--<script src="/assets/d002c34c/jquery.js"></script>-->
<table class="table table-responsive table-bordered">
    <tr>
        <td>id</td>
        <td>文章分类名</td>
        <td>排序</td>
        <td>简介</td>
        <td>状态</td>
        <td>操作</td>
    </tr>
    <?php foreach ($models as $model):?>
        <tr>
            <td><?=$model->id?></td>
            <td><?=$model->name?></td>
            <td><?=$model->sort?></td>
            <td><?=$model->intro?></td>
            <td><?=$model->status?'正常':'隐藏'?></td>
            <td>
                <a href="<?=\yii\helpers\Url::to(['/article-category/edit?id='.$model->id])?>" class="btn btn-info">编辑</a>
                <a href="javascript:;" class="btn btn-warning delete">删除</a>
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
);?>

<?php
$del_url=\yii\helpers\Url::to(['article-category/delete']);
$this->registerJs(new \yii\web\JsExpression(
        <<<js
      $('.delete').on('click',function () {
            var tr=$(this).closest('tr')
            var id=tr.find('td:first').text();
         if(confirm('确认删除么?')){
                $.post("{$del_url}",{id:id},function (data) {
                var data=JSON.parse(data);
                if(data.success){
                    alert('删除成功!')
                    tr.hide('slow');
                }
            })
        }
            
    })   
js
));?>



