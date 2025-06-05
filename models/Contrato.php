<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "contrato".
 *
 * @property int $id_contrato
 * @property int $identificacion
 * @property int $id_tipo_contrato
 * @property int $id_centro_trabajo
 * @property int $id_grupo_pago
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
        $this->horario_trabajo = strtoupper($this->horario_trabajo);
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
            [['identificacion','id_tipo_contrato', 'id_cargo', 'auxilio_transporte', 'id_tipo_cotizante', 'id_subtipo_cotizante', 'id_entidad_salud', 'id_entidad_pension', 'id_caja_compensacion', 'id_cesantia', 'id_arl', 'id_motivo_terminacion', 'contrato_activo',
                'id_centro_trabajo','id_grupo_pago', 'id_tiempo','genera_prorroga','dias_contrato','generar_liquidacion','ibp_cesantia_inicial', 'ibp_prima_inicial', 'ibp_recargo_nocturno'], 'integer'],
            [['fecha_inicio', 'fecha_final', 'ultimo_pago', 'ultima_prima', 'ultima_cesantia', 'ultima_vacacion', 'fecha_preaviso','fecha_creacion','fecha_editado'], 'safe'],
            [['salario'], 'number'],
            [['comentarios', 'funciones_especificas','observacion'], 'string'],
            [['descripcion','usuario_creador','usuario_editor'], 'string', 'max' => 100],
            [['tipo_salario'], 'string', 'max' => 20],
            [['horario_trabajo'], 'string', 'max' => 10],
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
            [['id_tiempo'], 'exist', 'skipOnError' => true, 'targetClass' => TiempoServicio::className(), 'targetAttribute' => ['id_tiempo' => 'id_tiempo']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_contrato' => 'Nro contrato',
            'identificacion' => 'Identificacion',
            'id_tipo_contrato' => 'Tipo Contrato',
            'id_tiempo' => 'Tiempo',
            'id_cargo' => 'Cargo',
            'descripcion' => 'Descripcion',
            'fecha_inicio' => 'Fecha Inicio',
            'fecha_final' => 'Fecha Final',
            'tipo_salario' => 'Tipo Salario',
            'salario' => 'Salario',
            'auxilio_transporte' => 'Auxilio Transporte',
            'horario_trabajo' => 'Horario Trabajo',
            'comentarios' => 'Comentarios',
            'funciones_especificas' => 'Funciones',
            'id_tipo_cotizante' => 'Tipo Cotizante',
            'id_subtipo_cotizante' => 'Subtipo Cotizante',
            'id_eps' => 'Tipo Salud',
            'id_entidad_salud' => 'Entidad Salud',
            'id_pension' => 'Tipo Pension',
            'id_entidad_pension' => 'Entidad Pension',
            'id_caja_compensacion' => 'Caja Compensacion',
            'id_cesantia' => 'Cesantia',
            'id_centro_trabajo' => 'Centro Trabajo',
            'id_grupo_pago' => 'Grupo Pago',
            'id_empleado' => 'Empleado',
            'id_arl' => 'Arl',
            'ultimo_pago' => 'Ultimo Pago',
            'ultima_prima' => 'Ultima Prima',
            'ultima_cesantia' => 'Ultima Cesantia',
            'ultima_vacacion' => 'Ultima Vacacion',
            'ibp_cesantia_inicial' => 'Ibp Cesantia Inicial',
            'ibp_prima_inicial' => 'Ibp Prima Inicial',
            'ibp_recargo_nocturno' => 'Recargo Nocturno',
            'id_motivo_terminacion' => 'Motivo Terminacion',
            'contrato_activo' => 'Contrato Activo',
            'ciudad_laboral' => 'Ciudad Laboral',
            'ciudad_contratado' => 'Ciudad Contratado',
            'genera_prorroga' => 'Genera_prorroga',
            'observacion' => 'Observacion',
            'fecha_preaviso' => 'Fecha preaviso',
            'dias_contrato' =>'Dias contratado',
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
    
    public function getGrupoPago()
    {
        return $this->hasOne(GrupoPago::className(), ['id_grupo_pago' => 'id_grupo_pago']);
    }
    public function getTiempoServicio()
    {
        return $this->hasOne(TiempoServicio::className(), ['id_tiempo' => 'id_tiempo']);
    }
    public function getPagoEps()
    {
        return $this->hasOne(ConfiguracionEps::className(), ['id_eps' => 'id_eps']);
    }
    public function getPagoPension()
    {
        return $this->hasOne(ConfiguracionPension::className(), ['id_pension' => 'id_pension']);
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
    public function getAuxilio()
    {
        if($this->auxilio_transporte == 1){
            $auxilio = "SI";
        }else{
            $auxilio = "NO";
        }
        return $auxilio;
    }
     public function getGeneraprorroga()
    {
        if($this->genera_prorroga == 1){
            $generaprorroga = "SI";
        }else{
            $generaprorroga = "NO";
        }
        return $generaprorroga;
    }
    
    
}
