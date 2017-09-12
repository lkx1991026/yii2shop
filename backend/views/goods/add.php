<?php
/**
 * @var $this \yii\web\View
 */
$this->registerCssFile('@web/ztree/css/zTreeStyle/zTreeStyle.css');
$this->registerJsFile('@web/ztree/js/jquery.ztree.core.js',['depends'=>\yii\web\JqueryAsset::className()]);
$goodsCategorys=\backend\models\GoodsCategory::getNodes();
$brands=\backend\models\Brand::find()->asArray()->all();
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($goods,'name')->textInput();
echo $form->field($goods,'logo')->hiddenInput();
//===================上传文件插件
echo \yii\bootstrap\Html::fileInput('test', NULL, ['id' => 'test']);
echo \flyok666\uploadifive\Uploadifive::widget([
            'url' => yii\helpers\Url::to(['x-upload']),
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
echo \yii\bootstrap\Html::img($goods->logo?$goods->logo:null,['id'=>'logo','style'=>'width:100px;']);
echo $form->field($goods,'goods_catgory_id')->hiddenInput();
echo '<ul id="treeDemo" class="ztree"></ul>';
echo $form->field($goods,'brand_id')->dropDownList(\yii\helpers\ArrayHelper::map($brands,'id','name'));
echo $form->field($goods,'market_price')->textInput();
echo $form->field($goods,'shop_price')->textInput();
echo $form->field($goods,'stock')->textInput();
echo $form->field($goods,'sort')->textInput();
echo $form->field($goods,'is_on_sale',['inline'=>true])->radioList(['下架','在售']);
echo $form->field($goods,'status',['inline'=>true])->radioList(['回收站','正常']);
echo $form->field($content,'content')->widget(\kucha\ueditor\UEditor::className(),[]);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn=info']);
\yii\bootstrap\ActiveForm::end();

$this->registerJs(new \yii\web\JsExpression(
    <<<JS
    var zTreeObj;
        // zTree 的参数配置，深入使用请参考 API 文档（setting 配置详解）
        var setting = {
            data: {
		        simpleData: 
		        {
                enable: true,
                idKey: "id",
                pIdKey: "parent_id",
                rootPId: 0
		        }
	        },
	        callback: {
		        onClick: function (event, treeId, treeNode) {
                    console.log(treeNode.id);
                    $('#goods-goods_catgory_id').val(treeNode.id)
                    }
                }
        };
        // zTree 的数据属性，深入使用请参考 API 文档（zTreeNode 节点数据详解）
        
        var zNodes ={$goodsCategorys};
        zTreeObj = $.fn.zTree.init($("#treeDemo"), setting, zNodes);
        zTreeObj.expandAll(true);
        var node=zTreeObj.getNodeByParam('id', "{$goods->goods_catgory_id}", null);
        console.log(node);
        zTreeObj.selectNode(node);
            
JS
));