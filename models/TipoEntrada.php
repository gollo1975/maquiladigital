<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tipo_entrada".
 *
 * @property int $id_entrada_tipo
 * @property string $concepto
 */
class TipoEntrada extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tipo_entrada';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['concepto'], 'required'],
            [['genera_cobro'], 'integer'],
            [['concepto'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_entrada_tipo' => 'Id Entrada Tipo',
            'concepto' => 'Concepto',
            'genera_cobro' => 'genera_cobro',
        ];
    }
}
