<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "balanceo".
 *
 * @property int $id_balanceo
 * @property int $idordenproduccion
 * @property int $cantidad_empleados
 * @property string $fecha_creacion
 * @property string $fecha_inicio
 * @property string $usuariosistema
 *
 * @property Ordenproduccion $ordenproduccion
 */
class Balanceo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'balanceo';
    }
 public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->observacion = strtolower($this->observacion); 
        $this->observacion = ucfirst($this->observacion);  
        return true;
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fecha_inicio','id_proceso_confeccion','id_planta'], 'required'],
            [['idordenproduccion', 'cantidad_empleados','idcliente','modulo','id_proceso_confeccion','id_planta','id_horario'], 'integer'],
            [['fecha_inicio','fecha_cierre_modulo','hora_cierre_modulo'], 'safe'],
            [['total_minutos','total_segundos','tiempo_operario','porcentaje','tiempo_balanceo','total_horas','hora_final_modulo','numero_dias_balanceo','total_eficiencia'],'number'],
            [['observacion','hora_inicio'],'string', 'max' => 150],
            //clases
            [['idordenproduccion'], 'exist', 'skipOnError' => true, 'targetClass' => Ordenproduccion::className(), 'targetAttribute' => ['idordenproduccion' => 'idordenproduccion']],
            [['idcliente'], 'exist', 'skipOnError' => true, 'targetClass' => Cliente::className(), 'targetAttribute' => ['idcliente' => 'idcliente']],
            [['id_proceso_confeccion'], 'exist', 'skipOnError' => true, 'targetClass' => ProcesoConfeccionPrenda::className(), 'targetAttribute' => ['id_proceso_confeccion' => 'id_proceso_confeccion']],
            [['id_planta'], 'exist', 'skipOnError' => true, 'targetClass' => PlantaEmpresa::className(), 'targetAttribute' => ['id_planta' => 'id_planta']],
            [['id_horario'], 'exist', 'skipOnError' => true, 'targetClass' => Horario::className(), 'targetAttribute' => ['id_horario' => 'id_horario']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_balanceo' => 'Id Balanceo',
            'idordenproduccion' => 'Orden producción:',
            'cantidad_empleados' => 'Cantidad Empleados:',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_inicio' => 'Fecha Inicio:',
            'observacion' => 'Observacion:',
            'usuariosistema' => 'Usuariosistema',
            'idcliente' => 'Cliente',
            'modulo' => 'Nro modulo',
            'tiempo_balanceo' => 'Sam balanceo:',
            'id_proceso_confeccion' => 'Proceso confeccion:',
            'id_planta' => 'Planta:',
            'hora_inicio' => 'Hora inicio:',
            'id_horario' =>'Horario:',
            'total_horas' => 'Numero horas:',
            'hora_final_modulo' => 'Total horas:',
            'fecha_cierre_modulo' => 'Fecha cierre modulo:',
            'numero_dias_balanceo' => 'Total dias',
            'total_eficiencia' => 'total_eficiencia',
            'hora_cierre_modulo' => 'Hora cierre modulo:',
            
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdenproduccion()
    {
        return $this->hasOne(Ordenproduccion::className(), ['idordenproduccion' => 'idordenproduccion']);
    }
    
     public function getCliente()
    {
        return $this->hasOne(Cliente::className(), ['idcliente' => 'idcliente']);
    }
     public function getProcesoconfeccion()
    {
        return $this->hasOne(ProcesoConfeccionPrenda::className(), ['id_proceso_confeccion' => 'id_proceso_confeccion']);
    }
     public function getPlantaempresa()
    {
        return $this->hasOne(PlantaEmpresa::className(), ['id_planta' => 'id_planta']);
    }
     public function getHorario()
    {
        return $this->hasOne(Horario::className(), ['id_horario' => 'id_horario']);
    }
    
    public function getNombreBalanceo()
    {
        return " Id: {$this->id_balanceo} - Nombre: {$this->procesoconfeccion->descripcion_proceso}";
    }
    
    public function getEstadomodulo() {
        if($this->estado_modulo == '0'){
            $estadomodulo = 'ABIERTO';
        }else{
            $estadomodulo = 'CERRADO';
        }
        
        return $estadomodulo;
    }
     public function getVerReproceso()
    {
        if($this->activo_reproceso == 0){
            $verreproceso = "ABIERTO";
        }else{
            $verreproceso = "CERRADO";
        }
        return $verreproceso;
    }
}
