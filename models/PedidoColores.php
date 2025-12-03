<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pedido_colores".
 *
 * @property int $id_entrada
 * @property int $id_detalle
 * @property int $id
 * @property int $id_pedido
 * @property int $cantidad
 *
 * @property PedidosDetalle $detalle
 * @property Color $id0
 * @property Pedidos $pedido
 */
class PedidoColores extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pedido_colores';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_detalle', 'id', 'id_pedido', 'cantidad','idtalla'], 'integer'],
            [['id_detalle'], 'exist', 'skipOnError' => true, 'targetClass' => PedidosDetalle::className(), 'targetAttribute' => ['id_detalle' => 'id_detalle']],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => Color::className(), 'targetAttribute' => ['id' => 'id']],
            [['idtalla'], 'exist', 'skipOnError' => true, 'targetClass' => Talla::className(), 'targetAttribute' => ['idtalla' => 'idtalla']],
            [['id_pedido'], 'exist', 'skipOnError' => true, 'targetClass' => Pedidos::className(), 'targetAttribute' => ['id_pedido' => 'id_pedido']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_entrada' => 'Id Entrada',
            'id_detalle' => 'Id Detalle',
            'id' => 'ID',
            'id_pedido' => 'Id Pedido',
            'cantidad' => 'Cantidad',
            'idtalla' => 'idtalla',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDetalle()
    {
        return $this->hasOne(PedidosDetalle::className(), ['id_detalle' => 'id_detalle']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getColores()
    {
        return $this->hasOne(Color::className(), ['id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPedido()
    {
        return $this->hasOne(Pedidos::className(), ['id_pedido' => 'id_pedido']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTallas()
    {
        return $this->hasOne(Talla::className(), ['idtalla' => 'idtalla']);
    }
    
     // muestra e nombre del color
    public function getNombreColor()
    {
        return " Color: {$this->colores->color}";
    }
}
