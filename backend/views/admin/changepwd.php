<?php
echo '<h1>修改密码</h1>';
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'username')->textInput();
echo $form->field($model,'password')->passwordInput();
echo $form->field($model,'newpassword')->passwordInput();
echo $form->field($model,'renewpassword')->passwordInput();
echo \yii\bootstrap\Html::submitButton('提交修改',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();