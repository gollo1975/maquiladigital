<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "programacion_nomina".
 *
 * @property int $id_programacion
 * @property int $id_grupo_pago
 * @property int $id_periodo_pago_nomina
 * @property int $id_contrato
 * @property int $id_empleado
 * @property int $cedula_empleado
 * @property string $fecha_inicio_contrato
 * @property string $fecha_desde
 * @property int $nro_pago
 * @property double $total_devengado
 * @property double $total_deduccion
 * @property double $ibc_prestacional
 * @property double $ibc_no_pestacional
 * @property double $total_licencia
 * @property double $total_incapacidad
 * @property double $total_tiempo_extra
 * @property double $total_recargo
 * @property string $fecha_hasta
 * @property string $fecha_real_corte
 * @property string $fecha_creacion
 * @property int $dias_pago
 * @property int $estado_generado
 * @property int $estado_liquidado
 * @property int $estado_cerrado
 * @property string $usuariosistema
 *
 * @property ProgramacionNominaDetalle $programacion
 * @property GrupoPago $grupoPago
 * @property PeriodoPagoNomina $periodoPagoNomina
 * @property Contrato $contrato
 * @property Empleado $empleado
 */
class ProgramacionNomina extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'programacion_nomina';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_grupo_pago', 'id_periodo_pago_nomina', 'id_contrato', 'id_empleado', 'cedula_empleado', 'fecha_inicio_contrato', 'fecha_desde', 'total_tiempo_extra', 'total_recargo', 'fecha_hasta', 'fecha_real_corte', 'usuariosistema','id_tipo_nomina'], 'required'],
            [['id_grupo_pago', 'id_periodo_pago_nomina', 'id_contrato', 'id_empleado', 'cedula_empleado', 'nro_pago', 'dias_pago', 'estado_generado', 'estado_liquidado', 'estado_cerrado', 'salario_contrato', 'dia_real_pagado', 'salario_medio_tiempo',
                'vlr_ibp_medio_tiempo','total_pagar', 'total_auxilio_transporte', 'salario_promedio', 'total_ibc_no_prestacional','id_tipo_nomina','dias_ausentes','dias_vacacion','horas_vacacion','ibc_vacacion','ajuste_incapacidad','importar_prima','pago_aplicado'], 'integer'],
            [['fecha_inicio_contrato', 'fecha_desde', 'fecha_hasta', 'fecha_real_corte', 'fecha_creacion', 'fecha_final_contrato', 'fecha_ultima_prima','fecha_ultima_vacacion','fecha_ultima_cesantia','fecha_inicio_vacacion','fecha_final_vacacion'], 'safe'],
            [['total_devengado', 'total_deduccion', 'ibc_prestacional', 'ibc_no_prestacional', 'total_licencia', 'total_incapacidad', 'total_tiempo_extra', 'total_recargo', 'horas_pago'], 'number'],
            [['usuariosistema'], 'string', 'max' => 30],
            ['tipo_salario', 'string'], 
            [['id_programacion'], 'exist', 'skipOnError' => true, 'targetClass' => ProgramacionNominaDetalle::className(), 'targetAttribute' => ['id_programacion' => 'id_programacion']],
            [['id_grupo_pago'], 'exist', 'skipOnError' => true, 'targetClass' => GrupoPago::className(), 'targetAttribute' => ['id_grupo_pago' => 'id_grupo_pago']],
            [['id_periodo_pago_nomina'], 'exist', 'skipOnError' => true, 'targetClass' => PeriodoPagoNomina::className(), 'targetAttribute' => ['id_periodo_pago_nomina' => 'id_periodo_pago_nomina']],
            [['id_contrato'], 'exist', 'skipOnError' => true, 'targetClass' => Contrato::className(), 'targetAttribute' => ['id_contrato' => 'id_contrato']],
            [['id_empleado'], 'exist', 'skipOnError' => true, 'targetClass' => Empleado::className(), 'targetAttribute' => ['id_empleado' => 'id_empleado']],
            [['id_tipo_nomina'], 'exist', 'skipOnError' => true, 'targetClass' => TipoNomina::className(), 'targetAttribute' => ['id_tipo_nomina' => 'id_tipo_nomina']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_programacion' => 'Nro programación',
            'id_grupo_pago' => 'Grupo pago',
            'id_periodo_pago_nomina' => 'Periodo Nomina',
            'id_contrato' => 'Nro contrato',
            'id_empleado' => 'Id Empleado',
            'cedula_empleado' => 'Documento',
            'fecha_inicio_contrato' => 'Fecha Inicio Contrato',
            'fecha_desde' => 'Fecha Desde',
            'nro_pago' => 'Nro Pago',
            'total_devengado' => 'Total Devengado',
            'total_deduccion' => 'Total Deduccion',
            'ibc_prestacional' => 'Ibc Prestacional',
            'ibc_no_prestacional' => 'Ibc No Pestacional',
            'total_licencia' => 'Total Licencia',
            'total_incapacidad' => 'Total Incapacidad',
            'total_tiempo_extra' => 'Total Tiempo Extra',
            'total_recargo' => 'Total Recargo',
            'fecha_hasta' => 'Fecha Hasta',
            'fecha_real_corte' => 'Fecha Real Corte',
            'fecha_creacion' => 'Fecha Creacion',
            'dias_pago' => 'Dias Pago',
            'estado_generado' => 'Estado Generado',
            'estado_liquidado' => 'Estado Liquidado',
            'estado_cerrado' => 'Estado Cerrado',
            'usuariosistema' => 'Usuariosistema',
            'pago_aplicado' =>'pago_aplicado',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgramacion()
    {
        return $this->hasOne(ProgramacionNominaDetalle::className(), ['id_programacion' => 'id_programacion']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGrupoPago()
    {
        return $this->hasOne(GrupoPago::className(), ['id_grupo_pago' => 'id_grupo_pago']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPeriodoPagoNomina()
    {
        return $this->hasOne(PeriodoPagoNomina::className(), ['id_periodo_pago_nomina' => 'id_periodo_pago_nomina']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoNomina()
    {
        return $this->hasOne(TipoNomina::className(), ['id_tipo_nomina' => 'id_tipo_nomina']);
    }
    
    public function getContrato()
    {
        return $this->hasOne(Contrato::className(), ['id_contrato' => 'id_contrato']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmpleado()
    {
        return $this->hasOne(Empleado::className(), ['id_empleado' => 'id_empleado']);
    }
}
