<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tipo_novedad".
 *
 * @property int $id_tipo_novedad
 * @property string $novedad
 * @property string $fecha_hora
 */
class TipoNovedad extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tipo_novedad';
    }
    public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->novedad = strtoupper($this->novedad);        
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['novedad'], 'required'],
            [['fecha_hora'], 'safe'],
            [['novedad'], 'string', 'max' => 40],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_tipo_novedad' => 'Codigo',
            'novedad' => 'Descripción',
            'fecha_hora' => 'Fecha / Hora',
        ];
    }
}
