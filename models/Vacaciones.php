<?php

namespace app\models;
use Yii;

/**
 * This is the model class for table "vacaciones".
 *
 * @property int $id_vacacion
 * @property int $id_empleado
 * @property int $id_contrato
 * @property int $id_grupo_pago
 * @property int $documento
 * @property string $fecha_desde
 * @property string $fecha_hasta
 * @property string $fecha_proceso
 * @property string $fecha_ingreso
 * @property string $fecha_inicio_disfrute
 * @property string $fecha_final_disfrute
 * @property int $dias_disfrutados
 * @property int $dias_pagados
 * @property int $dias_total_vacacion
 * @property int $dias_real_disfrutados
 * @property int $salario_contrato
 * @property int $salario_promedio
 * @property int $total_pago_vacacion
 * @property int $vlr_vacacion_disfrute
 * @property int $vlr_vacacion_dinero
 * @property int $vlr_recargo_nocturno
 * @property int $dias_ausentismo
 * @property int $descuento_eps
 * @property int $descuento_pension
 * @property int $total_descuentos
 * @property int $total_bonificaciones
 * @property int $estado_autorizado
 * @property int $estado_cerrado
 * @property int $estado_anulado
 * @property string $observacion
 * @property string $usuariosistema
 * @property int $nro_pago
 *
 * @property Empleado $empleado
 * @property Contrato $contrato
 * @property GrupoPago $grupoPago
 */
class Vacaciones extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vacaciones';
    }
    

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_empleado', 'dias_disfrutados','dias_pagados'], 'required'],
            [['id_empleado', 'id_contrato', 'id_grupo_pago', 'documento', 'dias_disfrutados', 'dias_pagados', 'dias_total_vacacion', 'dias_real_disfrutados', 'salario_contrato',
            'salario_promedio', 'total_pago_vacacion', 'vlr_vacacion_disfrute', 'vlr_vacacion_dinero', 'vlr_recargo_nocturno', 'dias_ausentismo', 'descuento_eps', 'descuento_pension',
            'total_descuentos', 'total_bonificaciones', 'estado_autorizado', 'estado_cerrado', 'estado_anulado', 'nro_pago','total_pagar','vlr_dia_vacacion',
                'dias_totales_periodo','dias_total_vacacion_pagados','vlr_vacacion_bruto','total_compensado','saldo_vacaciones'], 'integer'],
            [['fecha_desde_disfrute', 'fecha_hasta_disfrute', 'fecha_proceso', 'fecha_ingreso', 'fecha_inicio_periodo', 'fecha_final_periodo'], 'safe'],
            [['observacion'], 'string', 'max' => 100],
            [['usuariosistema'], 'string', 'max' => 20],
            [['id_empleado'], 'exist', 'skipOnError' => true, 'targetClass' => Empleado::className(), 'targetAttribute' => ['id_empleado' => 'id_empleado']],
            [['id_contrato'], 'exist', 'skipOnError' => true, 'targetClass' => Contrato::className(), 'targetAttribute' => ['id_contrato' => 'id_contrato']],
            [['id_grupo_pago'], 'exist', 'skipOnError' => true, 'targetClass' => GrupoPago::className(), 'targetAttribute' => ['id_grupo_pago' => 'id_grupo_pago']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_vacacion' => 'Id',
            'id_empleado' => 'Empleado:',
            'id_contrato' => 'Nro contrato',
            'id_grupo_pago' => 'Grupo Pago',
            'documento' => 'Documento',
            'fecha_desde_disfrute' => 'Fecha inicio de vacaciones:',
            'fecha_hasta_disfrute' => 'Fecha final de vacaciones:',
            'fecha_proceso' => 'F. Proceso',
            'fecha_ingreso' => 'Fecha inicio labores:',
            'fecha_inicio_disfrute' => 'F. inicio disfrute',
            'fecha_final_disfrute' => 'F. final disfrute',
            'dias_disfrutados' => 'Dias Disfrutados:',
            'dias_pagados' => 'Dias reconocidos en dinero:',
            'dias_total_vacacion' => 'Dias Total Vacacion',
            'dias_real_disfrutados' => 'Dias Real Disfrutados',
            'salario_contrato' => 'Salario Contrato',
            'salario_promedio' => 'Salario Promedio',
            'total_pago_vacacion' => 'Total Pago Vacacion',
            'vlr_vacacion_disfrute' => 'Vlr Vacacion Disfrute',
            'vlr_vacacion_dinero' => 'Vlr Vacacion Dinero',
            'vlr_recargo_nocturno' => 'Vlr Recargo Nocturno',
            'dias_ausentismo' => 'Dias Ausentismo',
            'descuento_eps' => 'Descuento Eps',
            'descuento_pension' => 'Descuento Pension',
            'total_descuentos' => 'Total Descuentos',
            'total_bonificaciones' => 'Total Bonificaciones',
            'total_pagar' => 'Total pagar:',
            'estado_autorizado' => 'Estado Autorizado',
            'estado_cerrado' => 'Estado Cerrado',
            'estado_anulado' => 'Estado Anulado',
            'observacion' => 'Observacion:',
            'usuariosistema' => 'Usuariosistema',
            'nro_pago' => 'Nro Pago',
            'total_compensado' => 'Total compensado:',
            'saldo_vacaciones' => 'saldo_vacaciones',
        ];
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
    public function getContrato()
    {
        return $this->hasOne(Contrato::className(), ['id_contrato' => 'id_contrato']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGrupoPago()
    {
        return $this->hasOne(GrupoPago::className(), ['id_grupo_pago' => 'id_grupo_pago']);
    }
    
    public function getEstadocerrado() {
        if ($this->estado_cerrado == 1){
            $estadocerrado = 'SI';
        }else{
            $estadocerrado = 'NO';
        }
        return $estadocerrado;
    }
    public function getProcesoanulado() {
        if ($this->estado_anulado == 1){
            $procesoanulado = 'SI';
        }else{
            $procesoanulado = 'NO';
        }
        return $procesoanulado;
    }
}
