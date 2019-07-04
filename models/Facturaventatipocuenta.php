<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "facturaventatipocuenta".
 *
 * @property int $id_factura_venta_tipo_cuenta
 * @property int $cuenta
 * @property int $tipocuenta
 * @property int $id_factura_venta_tipo
 * @property int $base
 * @property int $subtotal
 * @property int $iva
 * @property int $rete_fuente
 * @property int $rete_iva
 * @property int $total
 * @property int $base_rete_fuente
 * @property double $porcentaje_base
 *
 * @property Facturaventatipo $facturaVentaTipo
 */
class Facturaventatipocuenta extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'facturaventatipocuenta';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cuenta', 'tipocuenta', 'id_factura_venta_tipo'], 'required'],
            [['cuenta', 'tipocuenta', 'id_factura_venta_tipo', 'base', 'subtotal', 'iva', 'rete_fuente', 'rete_iva', 'total', 'base_rete_fuente'], 'integer'],
            [['porcentaje_base'], 'number'],
            [['id_factura_venta_tipo'], 'exist', 'skipOnError' => true, 'targetClass' => Facturaventatipo::className(), 'targetAttribute' => ['id_factura_venta_tipo' => 'id_factura_venta_tipo']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_factura_venta_tipo_cuenta' => 'Id Factura Venta Tipo Cuenta',
            'cuenta' => 'Cuenta',
            'tipocuenta' => 'Tipocuenta',
            'id_factura_venta_tipo' => 'Id Factura Venta Tipo',
            'base' => 'Base',
            'subtotal' => 'Subtotal',
            'iva' => 'Iva',
            'rete_fuente' => 'Rete Fuente',
            'rete_iva' => 'Rete Iva',
            'total' => 'Total',
            'base_rete_fuente' => 'Base Rete Fuente',
            'porcentaje_base' => '% Base',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFacturaVentaTipo()
    {
        return $this->hasOne(Facturaventatipo::className(), ['id_factura_venta_tipo' => 'id_factura_venta_tipo']);
    }
}
