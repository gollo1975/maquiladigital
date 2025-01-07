<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "forma_pago".
 *
 * @property int $id_forma_pago
 * @property string $concepto
 * @property int $codigo_api
 */
class FormaPago extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'forma_pago';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['concepto', 'codigo_api'], 'required'],
            [['codigo_api','codigo_api_nomina'], 'integer'],
            [['concepto'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_forma_pago' => 'Id Forma Pago',
            'concepto' => 'Concepto',
            'codigo_api' => 'Codigo Api',
            'codigo_api_nomina' => 'codigo_api_nomina'
        ];
    }
}
