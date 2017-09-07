<?php
$form=\yii\bootstrap\ActiveForm::begin();
//name	varchar(50)	名称
//intro	text	简介
//logo	varchar(255)	LOGO图片
//sort	int(11)	排序
//status	int(2)	状态(-1删除 0隐藏 1正常)
echo $form->field($model,'name')->textInput();
echo $form->field($model,'intro')->textarea();
echo $form->field($model,'file')->fileInput();
echo $form->field($model,'sort')->textInput();
echo $form->field($model,'status',['inline'=>true])->radioList(['隐藏','正常']);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);

\yii\bootstrap\ActiveForm::end();