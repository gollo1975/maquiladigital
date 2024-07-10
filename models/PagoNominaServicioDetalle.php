<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pago_nomina_servicio_detalle".
 *
 * @property int $id_detalle
 * @property int $id_pago
 * @property int $idordenproduccion
 * @property int $codigo_salario
 * @property int $devengado
 * @property int $deduccion
 *
 * @property PagoNominaServicios $pago
 * @property Ordenproduccion $ordenproduccion
 * @property ConceptoSalarios $codigoSalario
 */
class PagoNominaServicioDetalle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pago_nomina_servicio_detalle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_pago', 'codigo_salario', 'devengado', 'deduccion','id_credito'], 'integer'],
            [['devengado', 'deduccion'], 'required'],
            ['fecha_corte', 'safe'],
            [['id_pago'], 'exist', 'skipOnError' => true, 'targetClass' => PagoNominaServicios::className(), 'targetAttribute' => ['id_pago' => 'id_pago']],
            [['codigo_salario'], 'exist', 'skipOnError' => true, 'targetClass' => ConceptoSalarios::className(), 'targetAttribute' => ['codigo_salario' => 'codigo_salario']],
            [['id_credito'], 'exist', 'skipOnError' => true, 'targetClass' => CreditoOperarios::className(), 'targetAttribute' => ['id_credito' => 'id_credito']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_detalle' => 'Id Detalle',
            'id_pago' => 'Id Pago',
            'codigo_salario' => 'Codigo Salario',
            'devengado' => 'Devengado',
            'deduccion' => 'Deduccion',
            'id_credito' => 'id_credito',
            'fecha_corte' => 'fecha_corte',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPago()
    {
        return $this->hasOne(PagoNominaServicios::className(), ['id_pago' => 'id_pago']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCodigoSalario()
    {
        return $this->hasOne(ConceptoSalarios::className(), ['codigo_salario' => 'codigo_salario']);
    }
    
     public function getCreditoOperario()
    {
        return $this->hasOne(CreditoOperarios::className(), ['id_credito' => 'id_credito']);
    }
}
