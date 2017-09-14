<?php
/**
 * @var $this \yii\web\View;
 */
$form=\yii\bootstrap\ActiveForm::begin();
echo \yii\bootstrap\Html::fileInput('test', NULL, ['id' => 'test']);
echo \flyok666\uploadifive\Uploadifive::widget([
    'url' => yii\helpers\Url::to(['s-upload']),
    'id' => 'test',
    'csrf' => true,
    'renderTag' => false,
    'jsOptions' => [
        'formData'=>['goods_id' => $goods_id],
        'width' => 120,
        'height' => 40,
        'onError' => new \yii\web\JsExpression(<<<EOF
    function(file, errorCode, errorMsg, errorString) {
    console.log('The file ' + file.name + ' could not be uploaded: ' + errorString + errorCode + errorMsg);
}
EOF
        ),
        'onUploadComplete' => new \yii\web\JsExpression(<<<EOF
    function(file, data, response) {
    data = JSON.parse(data);
    if (data.error) {
    console.log(data.msg);
    } else {
    console.log(data.fileUrl);
//    $('#goodsgallery-path').val(data.fileUrl);
    $('#logo').attr('src',data.fileUrl);
    var html='<span data-id='+data.id+'><img src='+data.fileUrl+'><br/>'+
    '<a href="javascript:;" class="btn btn-warning glyphicon glyphicon-arrow-up del">删除</a></span><br/>';
    <!--var html1='<img src='+data.id+'/>'+-->
    <!--'<br/>'+-->
    <!--'<a href="<?=\yii\helpers\Url::to(['goods/gdel?id='+data.id+'])?>" class="btn btn-warning glyphicon glyphicon-arrow-up">删除该图片'+-->
    <!--'</a><br/>';-->
    $('#img').prepend(html);
    }
    }
EOF
        ),
    ]
]);

\yii\bootstrap\ActiveForm::end();

//=====================
?>
<div id='img'>
    <?php foreach($pics as $pic):?>
        <span data-id="<?=$pic->id?>"><img src="<?=$pic->path?$pic->path:null;?>"/><br/>
        <a href="javascript:;" class="btn btn-warning glyphicon glyphicon-arrow-up del">删除该图片</a></span><br/>
    <?php endforeach;?>

</div>

<?php $this->registerJs(new \yii\web\JsExpression(
        <<<JS
    $('#img').on('click','.del',function() {
        if(confirm('确认删除?')){
            var span=$(this).closest('span');
            var id=span.attr('data-id');        
            $.post('/goods/gdel',{id:id},function(data) {
                span.remove();
            })
            }
    })
JS

));

