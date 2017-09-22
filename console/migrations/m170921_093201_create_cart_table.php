<?php

use yii\db\Migration;

/**
 * Handles the creation of table `cart`.
 */
class m170921_093201_create_cart_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('cart', [
            'id' => $this->primaryKey(),
            'goods_id'=>$this->integer()->comment('商品id'),
            'amount'=>$this->integer()->comment('数量'),
            'member'=>$this->integer()->comment('用户id')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('cart');
    }
}