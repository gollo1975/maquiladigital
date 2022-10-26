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
            [['total_horas','tiempo_desayuno','tiempo_almuerzo'],'number'],
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
}
