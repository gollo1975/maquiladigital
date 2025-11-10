<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "precio_venta_inventario".
 *
 * @property int $id_detalle
 * @property int $id_lista
 * @property int $id_inventario
 * @property int $valor_venta
 * @property string $user_name
 *
 * @property ListaPrecios $lista
 * @property InventarioPuntoVenta $inventario
 */
class PrecioVentaInventario extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'precio_venta_inventario';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_lista', 'id_inventario', 'valor_venta'], 'integer'],
            [['valor_venta'], 'required'],
            [['user_name'], 'string', 'max' => 15],
            [['id_lista'], 'exist', 'skipOnError' => true, 'targetClass' => ListaPrecios::className(), 'targetAttribute' => ['id_lista' => 'id_lista']],
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
            'id_lista' => 'Id Lista',
            'id_inventario' => 'Id Inventario',
            'valor_venta' => 'Valor Venta',
            'user_name' => 'User Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLista()
    {
        return $this->hasOne(ListaPrecios::className(), ['id_lista' => 'id_lista']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInventario()
    {
        return $this->hasOne(InventarioPuntoVenta::className(), ['id_inventario' => 'id_inventario']);
    }
}
