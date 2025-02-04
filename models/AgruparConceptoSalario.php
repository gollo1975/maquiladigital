<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "agrupar_concepto_salario".
 *
 * @property int $id_agrupado
 * @property string $concepto
 */
class AgruparConceptoSalario extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'agrupar_concepto_salario';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['concepto'], 'required'],
            [['concepto'], 'string', 'max' => 30],
            [['tipo_movimiento'],'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_agrupado' => 'Id Agrupado',
            'concepto' => 'Concepto',
            'tipo_movimiento' => 'tipo_movimiento',
        ];
    }
    
  
}
