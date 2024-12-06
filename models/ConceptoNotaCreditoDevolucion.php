<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "concepto_nota_credito_devolucion".
 *
 * @property int $id_concepto
 * @property string $concepto
 */
class ConceptoNotaCreditoDevolucion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'concepto_nota_credito_devolucion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['concepto'], 'required'],
             [['codigo_interno'], 'integer'],
            [['concepto'], 'string', 'max' => 40],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_concepto' => 'Id Concepto',
            'concepto' => 'Concepto',
            'codigo_interno' => 'codigo_interno',
        ];
    }
}
