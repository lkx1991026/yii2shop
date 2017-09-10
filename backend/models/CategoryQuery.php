<?php
namespace backend\models;

use creocoder\nestedsets\NestedSetsBehavior;
use yii\db\ActiveQuery;

class CategoryQuery extends ActiveQuery{
    public function behaviors() {
        return [
            NestedSetsBehavior::className(),
        ];
    }
}