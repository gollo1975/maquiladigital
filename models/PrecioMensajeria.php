<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "precio_mensajeria".
 *
 * @property int $id_precio
 * @property string $concepto
 * @property int $valor_precio
 */
class PrecioMensajeria extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'precio_mensajeria';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['concepto', 'valor_precio'], 'required'],
            [['valor_precio'], 'integer'],
            [['concepto'], 'string', 'max' => 40],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_precio' => 'Id Precio',
            'concepto' => 'Concepto',
            'valor_precio' => 'Valor Precio',
        ];
    }
}
