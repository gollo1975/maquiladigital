<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "prestaciones_sociales".
 *
 * @property int $id_prestacion
 * @property int $id_empleado
 * @property int $id_contrato
 * @property int $documento
 * @property int $nro_pago
 * @property int $id_grupo_pago
 * @property string $fecha_inicio_contrato
 * @property string $fecha_termino_contrato
 * @property string $fecha_creacion
 * @property int $dias_primas
 * @property int $ibp_prima
 * @property int $dias_ausencia_prima
 * @property int $dias_cesantias
 * @property int $ibp_cesantias
 * @property int $dias_ausencia_primas
 * @property int $interes_cesantia
 * @property double $porcentaje_intreres
 * @property int $dias_vacaciones
 * @property int $ibp_vacaciones
 * @property int $dias_ausencia_vacaciones
 * @property int $total_deduccion
 * @property int $total_devengado
 * @property int $total_pagar
 * @property string $observacion
 * @property string $usuariosistema
 *
 * @property Empleado $empleado
 * @property Contrato $contrato
 * @property GrupoPago $grupoPago
 * @property PrestacionesSocialesDetalle[] $prestacionesSocialesDetalles
 */
class PrestacionesSociales extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'prestaciones_sociales';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_empleado', 'id_contrato', 'id_grupo_pago'], 'required'],
            [['id_empleado', 'id_contrato', 'documento', 'nro_pago', 'id_grupo_pago', 'dias_primas', 'ibp_prima', 'dias_ausencia_prima', 'dias_cesantias', 'ibp_cesantias', 'dias_ausencia_cesantias', 'interes_cesantia', 'dias_vacaciones', 'ibp_vacaciones', 'dias_ausencia_vacaciones',
                'total_deduccion', 'total_devengado', 'total_pagar','salario', 'estado_generado','estado_aplicado','estado_cerrado','total_indemnizacion','pago_aplicado'], 'integer'],
            [['fecha_inicio_contrato', 'fecha_termino_contrato', 'fecha_creacion','ultimo_pago_prima','ultimo_pago_cesantias','ultimo_pago_vacaciones'], 'safe'],
            [['porcentaje_interes'], 'number'],
            [['observacion'], 'string', 'max' => 100],
            [['usuariosistema'], 'string', 'max' => 30],
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
            'id_prestacion' => 'Código',
            'id_empleado' => 'Empleado',
            'id_contrato' => 'Nro contrato',
            'documento' => 'Documento',
            'nro_pago' => 'Nro Pago',
            'id_grupo_pago' => 'Grupo Pago',
            'fecha_inicio_contrato' => 'Inicio contrato',
            'fecha_termino_contrato' => 'Termino contrato',
            'fecha_creacion' => 'Fecha creacion',
            'dias_primas' => 'Dias primas',
            'ibp_prima' => 'Salario prima',
            'dias_ausencia_prima' => 'Dias ausencia prima',
            'dias_cesantias' => 'Dias cesantias',
            'ibp_cesantias' => 'Salario cesantias',
            'dias_ausencia_cesantia' => 'Dias ausencia cesantia',
            'interes_cesantia' => 'Interes cesantia',
            'porcentaje_interes' => 'Porcentaje interes',
            'dias_vacaciones' => 'Dias vacaciones',
            'ibp_vacaciones' => 'Salario vacaciones',
            'dias_ausencia_vacaciones' => 'Dias ausencia vacación',
            'total_deduccion' => 'Total deduccion',
            'total_devengado' => 'Total devengado',
            'total_pagar' => 'Total pagar',
            'observacion' => 'Observación',
            'usuariosistema' => 'Usuario',
            'ultimo_pago_cesantias' => 'Ultima cesantias',
            'ultimo_pago_vacaciones' => 'Ultima vacacion',
            'ultimo_pago_prima' => 'Ultima primas',
            'salario' => 'Salario',
            'pago_aplicado' => 'pago_aplicado',
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrestacionesSocialesDetalles()
    {
        return $this->hasMany(PrestacionesSocialesDetalle::className(), ['id_prestacion' => 'id_prestacion']);
    }
    public function getEstadogenerado()
    {
        if ($this->estado_generado == 1){
            $estadogenerado = 'SI';
        }else{
            $estadogenerado = 'NO';
        }
        return $estadogenerado ;
        
    }
    public function getEstadoaplicado()
    {
        if ($this->estado_aplicado == 1){
            $estadoaplicado = 'SI';
        }else{
            $estadoaplicado = 'NO';
        }
        return $estadoaplicado ;
        
    }
    public function getEstadocerrado()
    {
        if ($this->estado_cerrado == 1){
            $estadocerrado = 'SI';
        }else{
            $estadocerrado = 'NO';
        }
        return $estadocerrado ;
        
    }
}
