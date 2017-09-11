<?php
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'path')->hiddenInput();
echo \yii\bootstrap\Html::fileInput('test', NULL, ['id' => 'test']);
echo \flyok666\uploadifive\Uploadifive::widget([
'url' => yii\helpers\Url::to(['s-upload']),
'id' => 'test',
'csrf' => true,
'renderTag' => false,
'jsOptions' => [
'formData'=>['someKey' => 'someValue'],
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
    $('#goodsgallery-path').val(data.fileUrl);
    $('#logo').attr('src',data.fileUrl);
    }
    }
EOF
),
]
]);
echo \yii\helpers\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();

//=====================
echo \yii\bootstrap\Html::img($model->path?$model->path:null,['id'=>'logo','style'=>'width:100px;']);
