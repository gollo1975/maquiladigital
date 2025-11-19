<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "despacho_pedido_detalles".
 *
 * @property int $codigo
 * @property int $id_inventario
 * @property int $id_detalle
 * @property int $id_despacho
 * @property int $cantidad_despachada
 * @property int $valor_unitario
 * @property int $porcentaje_valor
 * @property int $valor_descuento
 * @property int $total_pagar
 *
 * @property InventarioPuntoVenta $inventario
 * @property PedidosDetalle $detalle
 * @property DespachoPedidos $despacho
 */
class DespachoPedidoDetalles extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'despacho_pedido_detalles';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_inventario', 'id_detalle', 'id_despacho', 'cantidad_despachada', 'valor_unitario', 'porcentaje_valor', 'valor_descuento', 'total_pagar'], 'integer'],
            [['id_inventario'], 'exist', 'skipOnError' => true, 'targetClass' => InventarioPuntoVenta::className(), 'targetAttribute' => ['id_inventario' => 'id_inventario']],
            [['id_detalle'], 'exist', 'skipOnError' => true, 'targetClass' => PedidosDetalle::className(), 'targetAttribute' => ['id_detalle' => 'id_detalle']],
            [['id_despacho'], 'exist', 'skipOnError' => true, 'targetClass' => DespachoPedidos::className(), 'targetAttribute' => ['id_despacho' => 'id_despacho']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'codigo' => 'Codigo',
            'id_inventario' => 'Id Inventario',
            'id_detalle' => 'Id Detalle',
            'id_despacho' => 'Id Despacho',
            'cantidad_despachada' => 'Cantidad Despachada',
            'valor_unitario' => 'Valor Unitario',
            'porcentaje_valor' => 'Porcentaje Valor',
            'valor_descuento' => 'Valor Descuento',
            'total_pagar' => 'Total Pagar',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInventario()
    {
        return $this->hasOne(InventarioPuntoVenta::className(), ['id_inventario' => 'id_inventario']);
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
    public function getDespacho()
    {
        return $this->hasOne(DespachoPedidos::className(), ['id_despacho' => 'id_despacho']);
    }
}
