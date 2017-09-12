<?php
?>
<h1><?=$model->name?></h1>
<h5><?=date('Y-m-d H:i:s',$model->create_time);?></h5>
<div id="myCarousel" class="carousel slide">
    <!-- 轮播（Carousel）指标 -->
    <ol class="carousel-indicators">
        <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
        <?php for($i=1;$i<$count;$i++):?>
            <li data-target="#myCarousel" data-slide-to="<?=$i?>"></li>
        <?php endfor;?>
    </ol>
    <!-- 轮播（Carousel）项目 -->
    <div class="carousel-inner">
        <?php foreach($gallerys as $k=>$gallery):?>
            <div class="item <?=$k?'':'active'?>">
            <img src="<?=$gallery->path?>" style="width:200px;">
        </div>
        <?php endforeach;?>
    </div>
    <!-- 轮播（Carousel）导航 -->
    <a class="carousel-control left" href="#myCarousel"
       data-slide="prev">&lsaquo;
    </a>
    <a class="carousel-control right" href="#myCarousel"
       data-slide="next">&rsaquo;
    </a>
</div>
<div id="intro">
    <?=$model->intro->content?>
</div>