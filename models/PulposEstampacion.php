<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pulpos_estampacion".
 *
 * @property int $id_pulpo
 * @property string $descripcion
 * @property int $cantidad_brazos
 * @property string $fecha_registro
 */
class PulposEstampacion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pulpos_estampacion';
    }
    
     public function beforeSave($insert) {
	if(!parent::beforeSave($insert)){
            return false;
        }
	# ToDo: Cambiar a cliente cargada de configuración.    
	$this->descripcion = strtoupper($this->descripcion);
	
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descripcion', 'cantidad_brazos'], 'required'],
            [['cantidad_brazos'], 'integer'],
            [['fecha_registro'], 'safe'],
            [['descripcion'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_pulpo' => 'Codigo',
            'descripcion' => 'Descripcion',
            'cantidad_brazos' => 'Cantidad de brazos',
            'fecha_registro' => 'Fecha de registro',
        ];
    }
}
