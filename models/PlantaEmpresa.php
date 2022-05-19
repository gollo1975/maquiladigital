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

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre_planta'], 'required'],
            [['telefono_planta', 'celular_planta'], 'integer'],
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
            'id_planta' => 'Id Planta',
            'nombre_planta' => 'Nombre Planta',
            'direccion_planta' => 'Direccion Planta',
            'telefono_planta' => 'Telefono Planta',
            'celular_planta' => 'Celular Planta',
            'usuariosistema' => 'Usuariosistema',
            'fecha_registro' => 'Fecha Registro',
        ];
    }
}
