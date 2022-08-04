<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "marca_maquinas".
 *
 * @property int $id_marca
 * @property string $descripcion
 */
class MarcaMaquinas extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'marca_maquinas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descripcion'], 'required'],
            [['descripcion'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_marca' => 'Id Marca',
            'descripcion' => 'Descripcion',
        ];
    }
}
