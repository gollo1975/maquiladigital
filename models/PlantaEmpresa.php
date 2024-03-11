<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "planta_empresa".
 *
 * @property int $id_planta
 * @property string $nombre_planta
 * @property string $direccion_planta
 * @property int $telefono_planta
 * @property int $celular_planta
 * @property string $usuariosistema
 * @property string $fecha_registro
 */
class PlantaEmpresa extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'planta_empresa';
    }

    public function beforeSave($insert) {
	if(!parent::beforeSave($insert)){
            return false;
        }
	# ToDo: Cambiar a empleado cargada de configuración.    
	$this->nombre_planta = strtoupper($this->nombre_planta);
	$this->direccion_planta = strtoupper($this->direccion_planta);
       
        
        return true;
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre_planta'], 'required'],
            [['telefono_planta', 'celular_planta'], 'string', 'max' => 15],
            [['fecha_registro'], 'safe'],
            [['nombre_planta', 'direccion_planta'], 'string', 'max' => 40],
            [['usuariosistema'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_planta' => 'Id',
            'nombre_planta' => 'Descripcion',
            'direccion_planta' => 'Direccion',
            'telefono_planta' => 'Telefono',
            'celular_planta' => 'Celular',
            'usuariosistema' => 'Usuariosistema',
            'fecha_registro' => 'Fecha Registro',
        ];
    }
}
