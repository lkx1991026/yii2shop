<?php
echo '<h1>添加管理员</h1>';
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'username')->textInput();
echo $form->field($model,'password')->textInput(['type'=>'password']);
echo $form->field($model,'repassword')->textInput(['type'=>'password']);
echo $form->field($model,'email')->textInput();
echo$form->field($model,'status',['inline'=>true])->radioList(['禁用','正常']);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();