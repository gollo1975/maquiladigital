<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "fondo_solidaridad_pensional".
 *
 * @property int $id_fsp
 * @property double $nro_salarios
 * @property double $porcentaje
 */
class FondoSolidaridadPensional extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fondo_solidaridad_pensional';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['rango1', 'rango2', ], 'integer'],
            [['porcentaje'], 'number'],
            [['detalle'], 'string'],
             [['codigo_api_nomina'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_fsp' => 'Id Fsp',
            'rango1' => 'rango1',
            'rango2' => 'rango2',
            'porcentaje' => 'Porcentaje',
        ];
    }
}
