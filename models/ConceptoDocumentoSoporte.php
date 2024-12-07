<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "concepto_documento_soporte".
 *
 * @property int $id_concepto
 * @property string $concepto
 * @property string $codigo_interface
 * @property string $user_name
 */
class ConceptoDocumentoSoporte extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'concepto_documento_soporte';
    }
    
     public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->concepto = strtoupper($this->concepto);        
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['concepto'], 'required'],
            [['concepto'], 'string', 'max' => 40],
            [['codigo_interface'], 'string', 'max' => 10],
            [['user_name'], 'string', 'max' => 15],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_concepto' => 'Codigo',
            'concepto' => 'Nombre del concepto',
            'codigo_interface' => 'Codigo interface',
            'user_name' => 'User Name',
        ];
    }
}
