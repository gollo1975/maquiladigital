<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "notacreditodetalle".
 *
 * @property int $iddetallenota
 * @property string $fecha
 * @property int $idfactura
 * @property int $nrofactura
 * @property double $valor
 * @property string $usuariosistema
 * @property int $idnotacredito
 *
 * @property Facturaventa $factura
 * @property Notacredito $notacredito
 */
class Notacreditodetalle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notacreditodetalle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fecha'], 'safe'],
            [['idfactura', 'nrofactura', 'idnotacredito','saldo_factura','cantidad','valor_retencion','valor_iva','valor_reteiva','total_nota','valor_nota_credito'], 'integer'],
            [['porcentaje_retefuente','porcentaje_iva','precio_unitario'],'number'],
            [['idnotacredito'], 'required'],
            [['usuariosistema'], 'string', 'max' => 50],
            [['idfactura'], 'exist', 'skipOnError' => true, 'targetClass' => Facturaventa::className(), 'targetAttribute' => ['idfactura' => 'idfactura']],
            [['idnotacredito'], 'exist', 'skipOnError' => true, 'targetClass' => Notacredito::className(), 'targetAttribute' => ['idnotacredito' => 'idnotacredito']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'iddetallenota' => 'Iddetallenota',
            'fecha' => 'Fecha',
            'idfactura' => 'Idfactura',
            'nrofactura' => 'Nrofactura',
            'saldo_factura' => 'Saldo',
            'cantidad' => 'cantidad',
            'valor_retencion' => 'valor_retencion',
            'valor_iva' => 'valor_iva',
            'valor_reteiva' => 'valor_reteiva',
            'total_nota' => 'total_nota',
            'porcentaje_retefuente' => 'porcentaje_retefuente',
            'porcentaje_iva' => 'porcentaje_iva',
            'precio_unitario' => 'precio_unitario',
            'usuariosistema' => 'Usuariosistema',
            'idnotacredito' => 'Idnotacredito',
            'valor_nota_credito' => 'valor_nota_credito',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFactura()
    {
        return $this->hasOne(Facturaventa::className(), ['idfactura' => 'idfactura']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotacredito()
    {
        return $this->hasOne(Notacredito::className(), ['idnotacredito' => 'idnotacredito']);
    }
}
