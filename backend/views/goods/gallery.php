<a href="<?=\yii\helpers\Url::to(['goods/addgallery?id='.$_GET['id']])?>" class="btn btn-info">添加相册</a><br/>
<?php
foreach($pics as $pic):?>
<img src="<?=$pic->path?$pic->path:null;?>"/><br/>
    <a href="<?=\yii\helpers\Url::to(['goods/gdel?id='.$pic->id])?>" class="btn btn-warning">删除该图片</a><br/>
<?php endforeach;?>

