<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "contrato".
 *
 * @property int $id_contrato
 * @property int $identificacion
 * @property int $id_tipo_contrato
 * @property int $tiempo_contrato
 * @property int $id_centro_trabajo
 * @property int $id_empleado
 * @property int $id_cargo
 * @property string $descripcion
 * @property string $fecha_inicio
 * @property string $fecha_final
 * @property string $tipo_salario
 * @property double $salario
 * @property int $auxilio_transporte
 * @property string $horario_trabajo
 * @property string $comentarios
 * @property string $funciones_especificas
 * @property int $id_tipo_cotizante
 * @property int $id_subtipo_cotizante
 * @property string $tipo_salud
 * @property int $id_entidad_salud
 * @property string $tipo_pension
 * @property int $id_entidad_pension
 * @property int $id_caja_compensacion
 * @property int $id_cesantia
 * @property int $id_arl
 * @property string $ultimo_pago
 * @property string $ultima_prima
 * @property string $ultima_cesantia
 * @property string $ultima_vacacion
 * @property double $ibp_cesantia_inicial
 * @property double $ibp_prima_inicial
 * @property double $ibp_recargo_nocturno
 * @property int $id_motivo_terminacion
 * @property int $contrato_activo
 * @property string $ciudad_laboral
 * @property string $ciudad_contratado
 *
 * @property TipoContrato $tipoContrato
 * @property MotivoTerminacion $motivoTerminacion
 * @property Municipio $ciudadLaboral
 * @property Municipio $ciudadContratado
 * @property Arl $arl
 * @property Cargo $cargo
 * @property TipoCotizante $tipoCotizante
 * @property SubtipoCotizante $subtipoCotizante
 * @property EntidadSalud $entidadSalud
 * @property EntidadPension $entidadPension
 * @property CajaCompensacion $cajaCompensacion
 * @property Cesantia $cesantia
 * @property Empleado $empleado
 */
class Contrato extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'contrato';
    }
    
    public function beforeSave($insert) {
	if(!parent::beforeSave($insert)){
            return false;
        }
	   
	$this->descripcion = strtoupper($this->descripcion);
	$this->comentarios = strtoupper($this->comentarios);
	$this->funciones_especificas = strtoupper($this->funciones_especificas);		
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['identificacion', 'tiempo_contrato','id_tipo_contrato', 'id_cargo', 'auxilio_transporte', 'id_tipo_cotizante', 'id_subtipo_cotizante', 'id_entidad_salud', 'id_entidad_pension', 'id_caja_compensacion', 'id_cesantia', 'id_arl', 'id_motivo_terminacion', 'contrato_activo','id_centro_trabajo'], 'integer'],
            [['fecha_inicio', 'fecha_final', 'ultimo_pago', 'ultima_prima', 'ultima_cesantia', 'ultima_vacacion'], 'safe'],
            [['salario', 'ibp_cesantia_inicial', 'ibp_prima_inicial', 'ibp_recargo_nocturno'], 'number'],
            [['comentarios', 'funciones_especificas'], 'string'],
            [['descripcion'], 'string', 'max' => 100],
            [['tipo_salario', 'tipo_salud'], 'string', 'max' => 20],
            [['horario_trabajo'], 'string', 'max' => 10],
            [['tipo_pension'], 'string', 'max' => 50],
            [['ciudad_laboral', 'ciudad_contratado'], 'string', 'max' => 15],
            [['id_tipo_contrato'], 'exist', 'skipOnError' => true, 'targetClass' => TipoContrato::className(), 'targetAttribute' => ['id_tipo_contrato' => 'id_tipo_contrato']],
            [['id_motivo_terminacion'], 'exist', 'skipOnError' => true, 'targetClass' => MotivoTerminacion::className(), 'targetAttribute' => ['id_motivo_terminacion' => 'id_motivo_terminacion']],
            [['ciudad_laboral'], 'exist', 'skipOnError' => true, 'targetClass' => Municipio::className(), 'targetAttribute' => ['ciudad_laboral' => 'idmunicipio']],
            [['ciudad_contratado'], 'exist', 'skipOnError' => true, 'targetClass' => Municipio::className(), 'targetAttribute' => ['ciudad_contratado' => 'idmunicipio']],
            [['id_arl'], 'exist', 'skipOnError' => true, 'targetClass' => Arl::className(), 'targetAttribute' => ['id_arl' => 'id_arl']],
            [['id_cargo'], 'exist', 'skipOnError' => true, 'targetClass' => Cargo::className(), 'targetAttribute' => ['id_cargo' => 'id_cargo']],
            [['id_tipo_cotizante'], 'exist', 'skipOnError' => true, 'targetClass' => TipoCotizante::className(), 'targetAttribute' => ['id_tipo_cotizante' => 'id_tipo_cotizante']],
            [['id_subtipo_cotizante'], 'exist', 'skipOnError' => true, 'targetClass' => SubtipoCotizante::className(), 'targetAttribute' => ['id_subtipo_cotizante' => 'id_subtipo_cotizante']],
            [['id_entidad_salud'], 'exist', 'skipOnError' => true, 'targetClass' => EntidadSalud::className(), 'targetAttribute' => ['id_entidad_salud' => 'id_entidad_salud']],
            [['id_entidad_pension'], 'exist', 'skipOnError' => true, 'targetClass' => EntidadPension::className(), 'targetAttribute' => ['id_entidad_pension' => 'id_entidad_pension']],
            [['id_caja_compensacion'], 'exist', 'skipOnError' => true, 'targetClass' => CajaCompensacion::className(), 'targetAttribute' => ['id_caja_compensacion' => 'id_caja_compensacion']],
            [['id_cesantia'], 'exist', 'skipOnError' => true, 'targetClass' => Cesantia::className(), 'targetAttribute' => ['id_cesantia' => 'id_cesantia']],
            [['id_empleado'], 'exist', 'skipOnError' => true, 'targetClass' => Empleado::className(), 'targetAttribute' => ['id_empleado' => 'id_empleado']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_contrato' => 'Id',
            'identificacion' => 'Identificacion',
            'id_tipo_contrato' => 'Tipo Contrato',
            'tiempo_contrato' => 'Tiempo',
            'id_cargo' => 'Cargo',
            'descripcion' => 'Descripcion',
            'fecha_inicio' => 'Fecha Inicio',
            'fecha_final' => 'Fecha Final',
            'tipo_salario' => 'Tipo Salario',
            'salario' => 'Salario',
            'auxilio_transporte' => 'Auxilio Transporte',
            'horario_trabajo' => 'Horario Trabajo',
            'comentarios' => 'Comentarios',
            'funciones_especificas' => 'Funciones Especificas',
            'id_tipo_cotizante' => 'Tipo Cotizante',
            'id_subtipo_cotizante' => 'Subtipo Cotizante',
            'tipo_salud' => 'Tipo Salud',
            'id_entidad_salud' => 'Entidad Salud',
            'tipo_pension' => 'Tipo Pension',
            'id_entidad_pension' => 'Entidad Pension',
            'id_caja_compensacion' => 'Caja Compensacion',
            'id_cesantia' => 'Cesantia',
            'id_centro_trabajo' => 'Centro Trabajo',
            'id_empleado' => 'Empleado',
            'id_arl' => 'Arl',
            'ultimo_pago' => 'Ultimo Pago',
            'ultima_prima' => 'Ultima Prima',
            'ultima_cesantia' => 'Ultima Cesantia',
            'ultima_vacacion' => 'Ultima Vacacion',
            'ibp_cesantia_inicial' => 'Ibp Cesantia Inicial',
            'ibp_prima_inicial' => 'Ibp Prima Inicial',
            'ibp_recargo_nocturno' => 'Ibp Recargo Nocturno',
            'id_motivo_terminacion' => 'Motivo Terminacion',
            'contrato_activo' => 'Contrato Activo',
            'ciudad_laboral' => 'Ciudad Laboral',
            'ciudad_contratado' => 'Ciudad Contratado',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoContrato()
    {
        return $this->hasOne(TipoContrato::className(), ['id_tipo_contrato' => 'id_tipo_contrato']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMotivoTerminacion()
    {
        return $this->hasOne(MotivoTerminacion::className(), ['id_motivo_terminacion' => 'id_motivo_terminacion']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCiudadLaboral()
    {
        return $this->hasOne(Municipio::className(), ['idmunicipio' => 'ciudad_laboral']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCiudadContratado()
    {
        return $this->hasOne(Municipio::className(), ['idmunicipio' => 'ciudad_contratado']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArl()
    {
        return $this->hasOne(Arl::className(), ['id_arl' => 'id_arl']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCargo()
    {
        return $this->hasOne(Cargo::className(), ['id_cargo' => 'id_cargo']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoCotizante()
    {
        return $this->hasOne(TipoCotizante::className(), ['id_tipo_cotizante' => 'id_tipo_cotizante']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubtipoCotizante()
    {
        return $this->hasOne(SubtipoCotizante::className(), ['id_subtipo_cotizante' => 'id_subtipo_cotizante']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEntidadSalud()
    {
        return $this->hasOne(EntidadSalud::className(), ['id_entidad_salud' => 'id_entidad_salud']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEntidadPension()
    {
        return $this->hasOne(EntidadPension::className(), ['id_entidad_pension' => 'id_entidad_pension']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCajaCompensacion()
    {
        return $this->hasOne(CajaCompensacion::className(), ['id_caja_compensacion' => 'id_caja_compensacion']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCesantia()
    {
        return $this->hasOne(Cesantia::className(), ['id_cesantia' => 'id_cesantia']);
    }
    
    public function getEmpleado()
    {
        return $this->hasOne(Empleado::className(), ['id_empleado' => 'id_empleado']);
    }
    
    public function getCentroTrabajo()
    {
        return $this->hasOne(CentroTrabajo::className(), ['id_centro_trabajo' => 'id_centro_trabajo']);
    }
    
    public function getActivo()
    {
        if($this->contrato_activo == 1){
            $activo = "SI";
        }else{
            $activo = "NO";
        }
        return $activo;
    }
}