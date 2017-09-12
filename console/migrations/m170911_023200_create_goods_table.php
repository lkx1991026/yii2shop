<?php

use yii\db\Migration;

/**
 * Handles the creation of table `goods`.
 */
class m170911_023200_create_goods_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('goods', [
            'id' => $this->primaryKey(),
            'name'=>$this->string(20)->comment('商品名称'),
            'sn'=>$this->string(20)->comment('货号'),
            'logo'=>$this->string()->comment('logo图片'),
            'goods_catgory_id'=>$this->integer()->comment('商品分类id'),
            'brand_id'=>$this->integer()->comment('品牌分类'),
            'market_price'=>$this->decimal()->comment('市场价格'),
            'shop_price'=>$this->decimal()->comment('商品价格'),
            'stock'=>$this->integer()->comment('库存'),
            'is_on_sale'=>$this->smallInteger()->comment('1在售0下架'),
            'status'=>$this->smallInteger()->comment('状态1正常0回收站'),
            'sort'=>$this->integer()->comment('排序'),
            'create_time'=>$this->integer()->comment('添加时间'),
            'view_times'=>$this->integer()->comment('浏览次数'),
            //stock	int	库存
            //is_on_sale	int(1)	是否在售(1在售 0下架)
            //status	inter(1)	状态(1正常 0回收站)
            //sort	int()	排序
            //create_time	int()	添加时间
            //view_times	int()	浏览次数

        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('goods');
    }
}