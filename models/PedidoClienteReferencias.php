<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pedido_cliente_referencias".
 *
 * @property int $id_referencia
 * @property int $id_pedido
 * @property int $codigo
 * @property string $referencia
 * @property int $id_detalle
 * @property int $valor_venta
 * @property int $iva
 * @property int $total_linea
 * @property string $user_name
 *
 * @property PedidoCliente $pedido
 * @property ReferenciaProducto $codigo0
 * @property ReferenciaListaPrecio $detalle
 */
class PedidoClienteReferencias extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pedido_cliente_referencias';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_pedido', 'codigo', 'id_detalle', 'valor_unitario','subtotal','cantidad', 'iva', 'total_linea','proceso_fabricacion'], 'integer'],
            [['porcentaje'],'number'],
            [['referencia'], 'string', 'max' => 40],
            [['user_name'], 'string', 'max' => 15],
            [['id_pedido'], 'exist', 'skipOnError' => true, 'targetClass' => PedidoCliente::className(), 'targetAttribute' => ['id_pedido' => 'id_pedido']],
            [['codigo'], 'exist', 'skipOnError' => true, 'targetClass' => ReferenciaProducto::className(), 'targetAttribute' => ['codigo' => 'codigo']],
            [['id_detalle'], 'exist', 'skipOnError' => true, 'targetClass' => ReferenciaListaPrecio::className(), 'targetAttribute' => ['id_detalle' => 'id_detalle']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_referencia' => 'Id Referencia',
            'id_pedido' => 'Id Pedido',
            'codigo' => 'Codigo',
            'referencia' => 'Referencia',
            'id_detalle' => 'Id Detalle',
            'valor_venta' => 'Valor Venta',
            'iva' => 'Iva',
            'subtotal' => 'Subtotal',
            'total_linea' => 'Total Linea',
            'user_name' => 'User Name',
            'cantidad' => 'cantidad',
            'porcentaje' => 'porcentaje',
            'proceso_fabricacion' => 'proceso_fabricacion',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPedido()
    {
        return $this->hasOne(PedidoCliente::className(), ['id_pedido' => 'id_pedido']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCodigoReferencia()
    {
        return $this->hasOne(ReferenciaProducto::className(), ['codigo' => 'codigo']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDetalle()
    {
        return $this->hasOne(ReferenciaListaPrecio::className(), ['id_detalle' => 'id_detalle']);
    }
}
