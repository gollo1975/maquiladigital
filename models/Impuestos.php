<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "impuestos".
 *
 * @property int $id_impuesto
 * @property double $valor
 *
 * @property Insumos[] $insumos
 */
class Impuestos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'impuestos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['valor'], 'required'],
            [['valor'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_impuesto' => 'Id Impuesto',
            'valor' => 'Valor',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInsumos()
    {
        return $this->hasMany(Insumos::className(), ['id_impuesto' => 'id_impuesto']);
    }
}
