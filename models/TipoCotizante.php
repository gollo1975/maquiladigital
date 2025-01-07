<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tipo_cotizante".
 *
 * @property int $id_tipo_cotizante
 * @property string $tipo
 *
 * @property Contrato[] $contratos
 */
class TipoCotizante extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tipo_cotizante';
    }

    public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->tipo = strtoupper($this->tipo);        
        return true;
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipo'], 'required'],
            [['tipo','codigo_api_nomina'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_tipo_cotizante' => 'Id Tipo Cotizante',
            'tipo' => 'Descripcion',
            'codigo_api_nomina' => 'Codigo api nomina',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContratos()
    {
        return $this->hasMany(Contrato::className(), ['id_tipo_cotizante' => 'id_tipo_cotizante']);
    }
}
