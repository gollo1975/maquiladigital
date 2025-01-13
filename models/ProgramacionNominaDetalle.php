<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "programacion_nomina_detalle".
 *
 * @property int $id_detalle
 * @property int $id_programacion
 * @property int $codigo_salario
 * @property int $horas_periodo
 * @property int $horas_periodo_reales
 * @property int $dias
 * @property int $dias_reales
 * @property int $dias_transporte
 * @property int $factor_dia
 * @property string $fecha_desde
 * @property string $fecha_hasta
 * @property double $salario_basico
 * @property double $vlr_devengado
 * @property double $vlr_deduccion
 * @property double $vlr_credito
 * @property double $vlr_hora
 * @property double $vlr_dia
 * @property double $vlr_neto_pagar
 * @property double $descuento_salud
 * @property double $descuento_pension
 * @property double $auxilio_transporte
 * @property double $vlr_licencia
 * @property double $nro_horas
 * @property double $vlr_incapacidad
 * @property double $vlr_ajuste_incapacidad
 * @property double $deduccion
 *
 * @property ProgramacionNomina $programacionNomina
 * @property ConceptoSalarios $codigoSalario
 */
class ProgramacionNominaDetalle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'programacion_nomina_detalle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_programacion', 'codigo_salario', 'horas_periodo', 'horas_periodo_reales', 'dias', 'dias_reales', 'dias_transporte', 'factor_dia', 'id_credito', 'dias_salario', 'id_grupo_pago', 'id_periodo_pago_nomina',
                'vlr_ibc_medio_tiempo','vlr_ajuste_incapacidad','vlr_vacacion','id_periodo_electronico'], 'integer'],
            [['fecha_desde', 'fecha_hasta'], 'safe'],
            [['salario_basico', 'vlr_devengado', 'vlr_deduccion', 'vlr_credito', 'vlr_hora', 'vlr_dia', 'vlr_neto_pagar', 'descuento_salud', 'descuento_pension', 'auxilio_transporte', 'vlr_licencia', 'nro_horas', 'vlr_incapacidad', 'vlr_pagar', 'deduccion','vlr_licencia_no_pagada'], 'number'],
            [['codigo_salario'], 'exist', 'skipOnError' => true, 'targetClass' => ConceptoSalarios::className(), 'targetAttribute' => ['codigo_salario' => 'codigo_salario']],
            [['id_programacion'], 'exist', 'skipOnError' => true, 'targetClass' => ProgramacionNominaDetalle::className(), 'targetAttribute' => ['id_programacion' => 'id_programacion']],
            [['id_credito'], 'exist', 'skipOnError' => true, 'targetClass' => Credito::className(), 'targetAttribute' => ['id_credito' => 'id_credito']],
            [['id_incapacidad'], 'exist', 'skipOnError' => true, 'targetClass' => Incapacidad::className(), 'targetAttribute' => ['id_incapacidad' => 'id_incapacidad']],
            [['id_licencia'], 'exist', 'skipOnError' => true, 'targetClass' => Licencia::className(), 'targetAttribute' => ['id_licencia' => 'id_licencia']],
            [['id_periodo_electronico'], 'exist', 'skipOnError' => true, 'targetClass' => PeriodoNominaElectronica::className(), 'targetAttribute' => ['id_periodo_electronico' => 'id_periodo_electronico']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_detalle' => 'Id Detalle',
            'id_programacion' => 'Id Programacion',
            'codigo_salario' => 'Codigo Salario',
            'horas_periodo' => 'Horas Periodo',
            'horas_periodo_reales' => 'Horas Periodo Reales',
            'dias' => 'Dias',
            'dias_reales' => 'Dias Reales',
            'dias_transporte' => 'Dias Transporte',
            'factor_dia' => 'Factor Dia',
            'fecha_desde' => 'Fecha Desde',
            'fecha_hasta' => 'Fecha Hasta',
            'salario_basico' => 'Salario Basico',
            'vlr_devengado' => 'Vlr Devengado',
            'vlr_deduccion' => 'Vlr Deduccion',
            'vlr_credito' => 'Vlr Credito',
            'vlr_hora' => 'Vlr Hora',
            'vlr_dia' => 'Vlr Dia',
            'vlr_neto_pagar' => 'Vlr Neto Pagar',
            'descuento_salud' => 'Descuento Salud',
            'descuento_pension' => 'Descuento Pension',
            'auxilio_transporte' => 'Auxilio Transporte',
            'vlr_licencia' => 'Vlr Licencia',
            'nro_horas' => 'Nro Horas',
            'vlr_incapacidad' => 'Vlr Incapacidad',
            'vlr_ajuste_incapacidad' => 'Vlr ajuste',
            'deduccion' => 'Deduccion',
            'vlr_ibc_medio_tiempo'=>'vlr_ibc_medio_tiempo',
            'id_periodo_electronico' => 'id_periodo_electronico',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgramacionNomina()
    {
        return $this->hasOne(ProgramacionNomina::className(), ['id_programacion' => 'id_programacion']);
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
    public function getPeriodoNominaElectronico()
    {
        return $this->hasOne(PeriodoNominaElectronica::className(), ['id_periodo_electronico' => 'id_periodo_electronico']);
    }
}
