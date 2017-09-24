<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "order".
 *
 * @property string $name
 * @property string $province
 * @property string $city
 * @property string $area
 * @property string $address
 * @property string $tel
 * @property integer $delivery_id
 * @property string $delivery_name
 * @property double $delivery_price
 * @property integer $payment_id
 * @property string $payment_name
 * @property string $total
 * @property integer $status
 * @property string $trade_no
 * @property integer $create_time
 * @property integer $id
 */
class Order extends \yii\db\ActiveRecord
{
    public static $deliveries=[
        0=>['顺分快递',20,'快得很'],
        1=>['圆通快递',5,'相因得很'],
        2=>['申通快递',10,'大城市配送快'],
        3=>['EMS',20,'偏远地区选这个']
    ];
    public static $payment=[
        0=>['微信支付','扫微信二维码支付'],
        1=>['支付宝支付','骚二维码支付'],
        2=>['网银支付','登陆网银支付']
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['delivery_id', 'payment_id', 'status', 'create_time'], 'integer'],
            [['delivery_price', 'total'], 'number'],
            [['name'], 'string', 'max' => 50],
            [['province', 'city', 'area'], 'string', 'max' => 20],
            [['address', 'delivery_name', 'payment_name', 'trade_no'], 'string', 'max' => 255],
            [['tel'], 'string', 'max' => 11],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => '收货人',
            'province' => '省',
            'city' => '市',
            'area' => '县',
            'address' => '详细地址',
            'tel' => '电话号码',
            'delivery_id' => '配送方式id',
            'delivery_name' => '配送方式名称',
            'delivery_price' => '配送方式价格',
            'payment_id' => '支付方式id',
            'payment_name' => '支付方式名称',
            'total' => '订单金额',
            'status' => '订单状态',
            'trade_no' => '第三方支付交易号',
            'create_time' => '创建时间',
            'id' => 'ID',
        ];
    }
    public function getOrders(){
        return $this->hasMany(OrderGoods::className(),['order_id','id']);
    }
}
