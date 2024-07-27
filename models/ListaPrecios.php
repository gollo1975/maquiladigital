<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lista_precios".
 *
 * @property int $id_lista
 * @property string $nombre_lista
 */
class ListaPrecios extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lista_precios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre_lista'], 'required'],
            [['nombre_lista'], 'string', 'max' => 15],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_lista' => 'Id Lista',
            'nombre_lista' => 'Nombre Lista',
        ];
    }
}
