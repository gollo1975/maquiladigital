<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "retencion_fuente".
 *
 * @property int $id_retencion
 * @property string $concepto
 * @property double $porcentaje
 */
class RetencionFuente extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'retencion_fuente';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['concepto', 'porcentaje'], 'required'],
            [['porcentaje'], 'number'],
            [['concepto'], 'string', 'max' => 15],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_retencion' => 'Id Retencion',
            'concepto' => 'Concepto',
            'porcentaje' => 'Porcentaje',
        ];
    }
}
