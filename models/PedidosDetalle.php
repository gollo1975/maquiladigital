<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pedidos_detalle".
 *
 * @property int $id_detalle
 * @property int $id_pedido
 * @property int $id_inventario
 * @property int $idtalla
 * @property int $id
 * @property int $cantidad
 * @property int $valor_unitario
 * @property double $porcentaje_descuento
 * @property int $valor_descuento
 * @property int $total_linea
 *
 * @property Pedidos $pedido
 * @property InventarioPuntoVenta $inventario
 * @property Talla $talla
 * @property Color $id0
 */
class PedidosDetalle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pedidos_detalle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_pedido', 'id_inventario', 'cantidad', 'valor_unitario', 'valor_descuento', 'total_linea','porcentaje_descuento','tipo_descuento','unidades_despachadas','unidades_faltantes'], 'integer'],
            [['user_name'], 'string','max' => 15],
            [['id_pedido'], 'exist', 'skipOnError' => true, 'targetClass' => Pedidos::className(), 'targetAttribute' => ['id_pedido' => 'id_pedido']],
            [['id_inventario'], 'exist', 'skipOnError' => true, 'targetClass' => InventarioPuntoVenta::className(), 'targetAttribute' => ['id_inventario' => 'id_inventario']],
            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_detalle' => 'Id Detalle',
            'id_pedido' => 'Id Pedido',
            'id_inventario' => 'Id Inventario',
            'cantidad' => 'Cantidad',
            'valor_unitario' => 'Valor Unitario',
            'porcentaje_descuento' => 'Porcentaje Descuento',
            'valor_descuento' => 'Valor Descuento',
            'total_linea' => 'Total Linea',
            'tipo_descuento' => 'tipo_descuento',
            'unidades_despachadas' => 'unidades_despachadas',
            'unidades_faltantes' => 'unidades_faltantes',
        ];
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
    public function getInventario()
    {
        return $this->hasOne(InventarioPuntoVenta::className(), ['id_inventario' => 'id_inventario']);
    }

    
}
