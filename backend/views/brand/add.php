<?php
use yii\web\JsExpression;

$form=\yii\bootstrap\ActiveForm::begin();
//name	varchar(50)	名称
//intro	text	简介
//logo	varchar(255)	LOGO图片
//sort	int(11)	排序
//status	int(2)	状态(-1删除 0隐藏 1正常)
echo $form->field($model,'name')->textInput();
echo $form->field($model,'intro')->textarea();
echo $form->field($model,'logo')->hiddenInput();
//===================上传文件插件
echo \yii\bootstrap\Html::fileInput('test', NULL, ['id' => 'test']);
echo \flyok666\uploadifive\Uploadifive::widget([
    'url' => yii\helpers\Url::to(['goods/x-upload']),
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
                 $('#goods-logo').val(data.fileUrl);
                 $('#logo').attr('src',data.fileUrl);
            }
        }
EOF
        ),
    ]
]);


//=====================
echo \yii\bootstrap\Html::img($model->logo?$model->logo:null,['id'=>'logo','style'=>'width:100px;']);
echo $form->field($model,'sort')->textInput();
echo $form->field($model,'status',['inline'=>true])->radioList(['隐藏','正常']);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);

\yii\bootstrap\ActiveForm::end();