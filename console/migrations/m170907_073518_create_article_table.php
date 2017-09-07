<?php

use yii\db\Migration;

/**
 * Handles the creation of table `article`.
 */
class m170907_073518_create_article_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('article', [
//            name	varchar(50)	名称
//            intro	text	简介
//            sort	int(11)	排序
//            status	int(2)	状态(-1删除 0隐藏 1正常)
            'id' => $this->primaryKey(),
            'name'=>$this->string(50)->comment('名称'),
            'intro'=>$this->text()->comment('简介'),
            'sort'=>$this->integer()->comment('排序'),
            'status'=>$this->smallInteger(2)->comment('-1删除 0隐藏 1正常')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('article');
    }
}
