<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tipos_maquinas".
 *
 * @property int $id_tipo
 * @property string $descripcion
 * @property int $estado
 *
 * @property MaquinaOperario[] $maquinaOperarios
 */
class TiposMaquinas extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tipos_maquinas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descripcion'], 'required'],
            [['estado','tiempo_mantenimiento'], 'integer'],
            [['descripcion'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_tipo' => 'Id Tipo',
            'descripcion' => 'Descripcion',
            'estado' => 'Estado',
            'tiempo_mantenimiento' => 'Tiempo mantenimiento',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaquinaOperarios()
    {
        return $this->hasMany(MaquinaOperario::className(), ['id_tipo' => 'id_tipo']);
    }
    
    public function getEstadomaquina() {
        if($this->estado == 1){
            $estadomaquina = 'SI';
        }else{
            $estadomaquina = 'NO';
        }
        return $estadomaquina;
    }
}
