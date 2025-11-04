<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "configuracion_iva".
 *
 * @property int $id_iva
 * @property double $valor_iva
 * @property int $predeterminado
 */
class ConfiguracionIva extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'configuracion_iva';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['valor_iva'], 'required'],
            [['valor_iva'], 'number'],
            [['predeterminado'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_iva' => 'Id Iva',
            'valor_iva' => 'Valor Iva',
            'predeterminado' => 'Predeterminado',
        ];
    }
}
