<?php
/**
 * @var $this \yii\web\View
 */
$this->registerCssFile('@web/ztree/css/zTreeStyle/zTreeStyle.css');
$this->registerJsFile('@web/ztree/js/jquery.ztree.core.js',['depends'=>\yii\web\JqueryAsset::className()]);

$goodsCategorys=\backend\models\GoodsCategory::getNodes();
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'parent_id')->hiddenInput();
//=================ztree================
echo '<ul id="treeDemo" class="ztree"></ul>';


//===========================ztree=========
echo $form->field($model,'intro')->textarea();
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
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
                    $('#goodscategory-parent_id').val(treeNode.id)
                    }
                }
        };
        // zTree 的数据属性，深入使用请参考 API 文档（zTreeNode 节点数据详解）
        
        var zNodes ={$goodsCategorys};
        zTreeObj = $.fn.zTree.init($("#treeDemo"), setting, zNodes);
        zTreeObj.expandAll(true);
        var node=zTreeObj.getNodeByParam('id', "{$model->parent_id}", null);
        console.log(node);
        zTreeObj.selectNode(node);
            
JS
));