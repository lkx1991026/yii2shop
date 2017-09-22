<?php

use yii\db\Migration;

/**
 * Handles the creation of table `address`.
 */
class m170918_085541_create_address_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('address', [
            'id' => $this->primaryKey(),
            'username'=>$this->integer()->comment('用户id'),
            'province_id'=>$this->integer()->comment('省份id'),
            'city_id'=>$this->integer()->comment('市id'),
            'area_id'=>$this->integer()->comment('区县id'),
            'addr'=>$this->string()->comment('详细地址'),
            'tel'=>$this->string()->comment('联系电话'),
            'is_default_addr'=>$this->smallInteger()->comment('默认收货地址'),
            'user_id'=>$this->smallInteger()->comment('用户id')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('address');
    }
}
