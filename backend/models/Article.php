<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "article".
 *
 * @property integer $id
 * @property string $name
 * @property string $intro
 * @property integer $article_category_id
 * @property integer $sort
 * @property integer $status
 * @property integer $create_time
 */
class Article extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['intro'], 'string'],
            [['article_category_id', 'sort', 'status'], 'integer'],
            [['name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '文章名',
            'intro' => '简介',
            'article_category_id' => '文章分类id',
            'sort' => '排序',
            'status' => '状态(-1删除 0隐藏 1正常)',
            'create_time' => '创建时间',
            'content'=>'内容详情'
        ];
    }
    public function getArticleCategory(){
        return $this->hasOne(ArticleCategory::className(),['id'=>'article_category_id']);
    }
    public function getArticleDetail(){
        return $this->hasOne(ArticleDetail::className(),['article_id'=>'id']);
    }
    public function behaviors()
    {
        return [

            ['class' =>TimestampBehavior::className(),
                'createdAtAttribute' => 'create_time',// 自己根据数据库字段修改
                'updatedAtAttribute' => 'create_time', // 自己根据数据库字段修改, // 自己根据数据库字段修改
            ]];
    }
}
