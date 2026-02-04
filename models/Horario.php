<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "horario".
 *
 * @property int $id_horario
 * @property string $horario
 * @property string $desde
 * @property string $hasta
 *
 * @property Fichatiempo[] $fichatiempos
 */
class Horario extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'horario';
    }
     public function beforeSave($insert) {
	if(!parent::beforeSave($insert)){
            return false;
        }
	# ToDo: Cambiar a cliente cargada de configuración.    
	$this->abreviatura = strtoupper($this->abreviatura);
	
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['horario', 'desde', 'hasta', 'total_horas'], 'required'],
            [['desde', 'hasta','abreviatura'], 'string'],
            [['total_horas','tiempo_desayuno','tiempo_almuerzo','minutos_desuso','minutos_sam_maquina','minutos_sam_salud','total_porcentaje_autorizado'],'number'],
            [['aplica_tiempo_desuso','total_eventos_dia','aplica_sam_maquina','total_evento_maquinas','aplica_sam_salud_ocupacional','total_evento_salud','aplica_tiempo_adicional'],'integer'],
            [['horario'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_horario' => 'Id',
            'horario' => 'Horario',
            'desde' => 'Desde',
            'hasta' => 'Hasta',
            'total_horas' =>'Total horas',
            'tiempo_desayuno' => 'Minuto desayuno',
            'tiempo_almuerzo' => 'Minuto almuerzo',
            'abreviatura' => 'Abreviatura',
            'aplica_tiempo_desuso' => 'Aplica tiempo en desuso',
            'minutos_desuso' => 'Minutos en desuso',
            'total_eventos_dia' => 'Total eventos x dia',
            'minutos_sam_maquina' => 'Minutos x maquina:',
            'minutos_sam_salud' => 'Minutos SO',
            'aplica_sam_maquina' => 'Aplica sam a maquinas',
            'total_evento_maquinas' => 'Total eventos para maquinas',
            'aplica_sam_salud_ocupacional' => 'Aplica sam salud ocupacional',
            'total_evento_salud' => 'Total eventos salud ocupacional',
            'aplica_tiempo_adicional' => 'Aplicar tiempo adicional',
            'total_porcentaje_autorizado' => 'Total porcentaje autorizado'
            
            
            
            
            
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFichatiempos()
    {
        return $this->hasMany(Fichatiempo::className(), ['id_horario' => 'id_horario']);
    }
    public function getOperarios()
    {
        return $this->hasMany(Operarios::className(), ['id_operario' => 'id_operario']);
    }
    
    public function getNombreHorario()
    {
        return "{$this->horario} {$this->desde} - {$this->hasta}";
    }
    
    public function getAplicaTiempoDesuso(){
        if($this->aplica_tiempo_desuso == 0){
            $aplicatiempodesuso = 'NO';
        }else{
            $aplicatiempodesuso = 'SI';
        }
        return $aplicatiempodesuso;
    }
     public function getAplicaTiempoSalud(){
        if($this->aplica_sam_salud_ocupacional == 0){
            $aplicatiemposalud = 'NO';
        }else{
            $aplicatiemposalud = 'SI';
        }
        return $aplicatiemposalud;
    }
     public function getAplicaTiempoMaquina(){
        if($this->aplica_sam_maquina == 0){
            $aplicatiempomaquina= 'NO';
        }else{
            $aplicatiempomaquina = 'SI';
        }
        return $aplicatiempomaquina;
    }
    
     public function getAplicaTiempoAdicional(){
        if($this->aplica_tiempo_adicional == 0){
            $aplicatiempoadicional= 'NO';
        }else{
            $aplicatiempoadicional = 'SI';
        }
        return $aplicatiempoadicional;
    }
    
} 
