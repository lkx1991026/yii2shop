<?php
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'parent_id')->dropDownList(\backend\models\Menu::getMenuList());
echo $form->field($model,'link')->dropDownList(\yii\helpers\ArrayHelper::map(Yii::$app->authManager->getPermissions(),'name','name'));
echo $form->field($model,'sort')->textInput();
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();