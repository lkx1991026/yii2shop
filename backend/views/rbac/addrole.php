<?php
/**
 * @var $this \yii\web\View
 */
$actionId=$this->context->action->id;
$name=substr($actionId,0,4)=='edit'?'修改':'添加';
echo "<h1>{$name}角色</h1>";
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'description')->textInput();
echo $form->field($model,'permissions')->checkboxList(\backend\models\AddPermissionForm::getPermissions());
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);

\yii\bootstrap\ActiveForm::end();