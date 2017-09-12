<?php
/* @var $this yii\web\View */
$form=\yii\bootstrap\ActiveForm::begin(['method'=>'get','action'=>['/goods/index']]);
echo $form->field($model,'name')->textInput();
echo $form->field($model,'sn')->textInput();
echo $form->field($model,'min')->textInput();
echo $form->field($model,'max')->textInput();
echo \yii\helpers\Html::submitButton('搜索',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();
?>
<style>
    #recycle{
        display:none;
    }
</style>

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
$this->registerJs(new \yii\web\JsExpression(
        <<<JS
    $('#recycle-btn').on('click',function() {
        $('#recycle').toggle('slow');
        $('#recycle-container').empty();
        $.get('/goods/recycle',function(data) {
            var data=JSON.parse(data);
            $.each(data,function(i,v) {
                 var html= '<tr>' +
                '<td>'+v.id+'</td>' +
                 '<td>'+v.name+'</td>' +
                  '<td>'+v.sn+'</td>' +
                   '<td><img src='+v.logo+' style="width:50px;"></td>' +
                    '<td>'+v.market_price+'</td>' +
                     '<td>'+v.shop_price+'</td>' +
                        '<td>' +
                            '<a href="javascript:;" class="btn btn-primary btn-sm recovery">恢复</a>' +
                             '</td>' +
                              '</tr>'
             $('#recycle-container').append(html);
            })
        })
    })
    $('table').on('click','.recovery',function() {
        var tr=$(this).closest('tr');
        var id=tr.find('td:first').text();
         var   self=tr;
        console.debug(id);
        $.post('/goods/recovery',{id:id},function(data) {
                var data=JSON.parse(data);
                if(data.success==true){
                    alert('恢复成功!');
                    self.remove();
                }else{
                    alert(data.msg);
                }
        })
    })
JS

))
?>
<a href="javascript:;" class="btn btn-info col-lg-offset-11" id="recycle-btn">回收站</a>
<div id="recycle">
    <table class="table table-bordered table-responsive" >
        <tr>
            <td>id</td>
            <td>商品名称</td>
            <td>商品货号</td>
            <td>商品图片</td>
            <td>商品市场价</td>
            <td>商品售价</td>
            <td>操作</td>
        </tr>
        <tbody id="recycle-container"></tbody>
    </table >
</div>

