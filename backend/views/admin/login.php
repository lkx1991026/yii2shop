<?php
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'username')->textInput();
echo $form->field($model,'login_pwd')->textInput(['type'=>'password']);
echo $form->field($model,'code')->widget(\yii\captcha\Captcha::className(),[
    'captchaAction'=>'admin/captcha',
    'template'=>'<div class="row"><div class="col-lg-1">{image}</div><div class="col-lg-1">{input}</div></div>']);
echo $form->field($model,'remember')->checkbox();
echo \yii\bootstrap\Html::submitButton('提交登陆',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();