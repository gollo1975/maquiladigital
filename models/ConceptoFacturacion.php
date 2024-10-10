<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "concepto_facturacion".
 *
 * @property int $id
 * @property string $concepto
 * @property double $porcentaje_retencion
 * @property double $porcenta_iva
 * @property string $codigo_interfaz
 */
class ConceptoFacturacion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'concepto_facturacion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['concepto', 'codigo_interfaz'], 'required'],
            [['porcentaje_retencion', 'porcentaje_iva'], 'number'],
            [['concepto'], 'string', 'max' => 40],
            [['codigo_interfaz'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'concepto' => 'Concepto',
            'porcentaje_retencion' => 'Porcentaje Retencion',
            'porcentaje_iva' => 'Porcenta Iva',
            'codigo_interfaz' => 'Codigo Interfaz',
        ];
    }
}
