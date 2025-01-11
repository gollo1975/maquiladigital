<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "subtipo_cotizante".
 *
 * @property int $id_subtipo_cotizante
 * @property string $subtipo
 *
 * @property Contrato[] $contratos
 */
class SubtipoCotizante extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subtipo_cotizante';
    }
    
    public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->subtipo = strtoupper($this->subtipo);        
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subtipo'], 'required'],
            [['subtipo','codigo_api_nomina'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_subtipo_cotizante' => 'Id',
            'subtipo' => 'Subtipo',
            'codigo_api_nomina' => 'Codigo api nomina',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContratos()
    {
        return $this->hasMany(Contrato::className(), ['id_subtipo_cotizante' => 'id_subtipo_cotizante']);
    }
}
