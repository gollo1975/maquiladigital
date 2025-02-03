<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "nomina_electronica_detalle".
 *
 * @property int $id_detalle
 * @property int $id_nomina_electronica
 * @property int $codigo_salario
 * @property string $decripcion
 * @property int $devengado_deduccion
 * @property string $fecha_inicio
 * @property string $fecha_final
 * @property double $devengado
 * @property double $deduccion
 * @property int $total_dias
 * @property double $porcentaje
 * @property double $auxilio_transporte
 * @property int $id_agrupado
 *
 * @property NominaElectronica $nominaElectronica
 * @property ConceptoSalarios $codigoSalario
 * @property AgruparConceptoSalario $agrupado
 */
class NominaElectronicaDetalle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nomina_electronica_detalle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_nomina_electronica', 'codigo_salario', 'devengado_deduccion', 'total_dias', 'id_agrupado','id_empleado','dias_prima','dias_cesantias','dias_incapacidad','dias_licencia_noremuneradas','codigo_incapacidad',
                'devengado', 'deduccion',  'auxilio_transporte','deduccion_pension','deduccion_eps','deduccion_fondo_solidaridad','valor_pago_prima',
                'valor_pago_cesantias','valor_pago_incapacidad','valor_pago_licencia','valor_pago_intereses'], 'integer'],
            [['fecha_inicio', 'fecha_final','inicio_incapacidad','final_incapacidad','inicio_licencia','final_licencia'], 'safe'],
            [['porcentaje','porcentaje_intereses'], 'number'],
            [['descripcion'], 'string', 'max' => 40],
            [['id_nomina_electronica'], 'exist', 'skipOnError' => true, 'targetClass' => NominaElectronica::className(), 'targetAttribute' => ['id_nomina_electronica' => 'id_nomina_electronica']],
            [['codigo_salario'], 'exist', 'skipOnError' => true, 'targetClass' => ConceptoSalarios::className(), 'targetAttribute' => ['codigo_salario' => 'codigo_salario']],
            [['id_agrupado'], 'exist', 'skipOnError' => true, 'targetClass' => AgruparConceptoSalario::className(), 'targetAttribute' => ['id_agrupado' => 'id_agrupado']],
            [['id_empleado'], 'exist', 'skipOnError' => true, 'targetClass' => Empleado::className(), 'targetAttribute' => ['id_empleado' => 'id_empleado']],
            [['codigo_incapacidad'], 'exist', 'skipOnError' => true, 'targetClass' => ConfiguracionIncapacidad::className(), 'targetAttribute' => ['codigo_incapacidad' => 'codigo_incapacidad']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_detalle' => 'Id Detalle',
            'id_nomina_electronica' => 'Id Nomina Electronica',
            'codigo_salario' => 'Codigo Salario',
            'descripcion' => 'Decripcion',
            'devengado_deduccion' => 'Devengado Deduccion',
            'fecha_inicio' => 'Fecha Inicio',
            'fecha_final' => 'Fecha Final',
            'devengado' => 'Devengado',
            'deduccion' => 'Deduccion',
            'total_dias' => 'Total Dias',
            'porcentaje' => 'Porcentaje',
            'auxilio_transporte' => 'Auxilio Transporte',
            'id_agrupado' => 'Id Agrupado',
            'id_empleado' => 'id_empleado',
            'deduccion_eps' => 'deduccion_eps',
            'deduccion_pension'=> 'deduccion_pension',
            'deduccion_fondo_solidaridad' => 'deduccion_fondo_solidaridad',
            'valor_pago_cesantias' => 'valor_pago_cesantias',
            'valor_pago_prima' => 'valor_pago_prima',
            'dias_prima' => 'dias_prima',
            'dias_cesantias' => 'dias_cesantias',
            'porcentaje_intereses' => 'porcentaje_intereses',
            'valor_pago_intereses'=> 'valor_pago_intereses',
            'codigo_incapacidad' => 'codigo_incapacidad', 
            
            
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNominaElectronica()
    {
        return $this->hasOne(NominaElectronica::className(), ['id_nomina_electronica' => 'id_nomina_electronica']);
    }
    
     /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmpleado()
    {
        return $this->hasOne(Empleado::className(), ['id_empleado' => 'id_empleado']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCodigoSalario()
    {
        return $this->hasOne(ConceptoSalarios::className(), ['codigo_salario' => 'codigo_salario']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgrupado()
    {
        return $this->hasOne(AgruparConceptoSalario::className(), ['id_agrupado' => 'id_agrupado']);
    }
    
     /**
     * @return \yii\db\ActiveQuery
     */
    public function getConfiguracionIncapacidad()
    {
        return $this->hasOne(ConfiguracionIncapacidad::className(), ['codigo_incapacidad' => 'codigo_incapacidad']);
    }
}
