<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "debaja_maquina".
 *
 * @property int $id
 * @property int $id_maquina
 * @property string $fecha_proceso
 * @property string $fecha_registro
 * @property string $observacion
 * @property int $usuario
 *
 * @property Maquinas $maquina
 */
class DebajaMaquina extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'debaja_maquina';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fecha_proceso', 'observacion'], 'required'],
            [['id_maquina'], 'integer'],
            [['fecha_proceso', 'fecha_registro'], 'safe'],
            [['observacion'], 'string', 'max' => 220],
            [['usuario'], 'string', 'max' => 15],
            [['id_maquina'], 'exist', 'skipOnError' => true, 'targetClass' => Maquinas::className(), 'targetAttribute' => ['id_maquina' => 'id_maquina']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_maquina' => 'Id Maquina',
            'fecha_proceso' => 'Fecha Proceso',
            'fecha_registro' => 'Fecha Registro',
            'observacion' => 'Observacion',
            'usuario' => 'Usuario',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaquina()
    {
        return $this->hasOne(Maquinas::className(), ['id_maquina' => 'id_maquina']);
    }
}
