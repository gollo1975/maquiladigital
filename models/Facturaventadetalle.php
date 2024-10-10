<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "facturaventadetalle".
 *
 * @property int $iddetallefactura
 * @property int $idfactura
 * @property int $idproductodetalle
 * @property string $codigoproducto
 * @property int $cantidad
 * @property double $preciounitario
 * @property double $total
 *
 * @property Productodetalle $productodetalle
 * @property Facturaventa $factura
 */
class Facturaventadetalle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'facturaventadetalle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idfactura', 'idproductodetalle', 'codigoproducto', 'cantidad', 'preciounitario', 'total'], 'required'],
            [['idfactura', 'idproductodetalle', 'cantidad','id'], 'integer'],
            [['preciounitario', 'total','porcentaje_iva','porcentaje_retefuente','valor_retencion','valor_iva','total_linea'], 'number'],
            [['codigoproducto'], 'string', 'max' => 15],
            [['idproductodetalle'], 'exist', 'skipOnError' => true, 'targetClass' => Productodetalle::className(), 'targetAttribute' => ['idproductodetalle' => 'idproductodetalle']],
            [['idfactura'], 'exist', 'skipOnError' => true, 'targetClass' => Facturaventa::className(), 'targetAttribute' => ['idfactura' => 'idfactura']],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => ConceptoFacturacion::className(), 'targetAttribute' => ['id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'iddetallefactura' => 'Iddetallefactura',
            'idfactura' => 'Idfactura',
            'idproductodetalle' => 'Idproductodetalle',
            'codigoproducto' => 'Codigoproducto',
            'cantidad' => 'Cantidad',
            'preciounitario' => 'Preciounitario',
            'total' => 'Total',
            'valor_iva' => 'valor_iva',
            'valor_retencion' => 'valor_retencion',
            'porcentaje_retefuente' => 'porcentaje_retefuente',
            'porcentaje_iva' => 'porcentaje_iva',
            'total_linea' => 'Total linea',
            'id' => 'Concepto',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductodetalle()
    {
        return $this->hasOne(Productodetalle::className(), ['idproductodetalle' => 'idproductodetalle']);
    }
   
     /**
     * @return \yii\db\ActiveQuery
     */
    public function getConceptoFactura()
    {
        return $this->hasOne(ConceptoFacturacion::className(), ['id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFactura()
    {
        return $this->hasOne(Facturaventa::className(), ['idfactura' => 'idfactura']);
    }
}
