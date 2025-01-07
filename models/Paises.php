<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "paises".
 *
 * @property int $id_pais
 * @property string $nombre_pais
 * @property string $codigo_interface
 */
class Paises extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'paises';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre_pais'], 'required'],
            [['nombre_pais'], 'string', 'max' => 40],
            [['codigo_interface'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_pais' => 'Id Pais',
            'nombre_pais' => 'Nombre Pais',
            'codigo_interface' => 'Codigo Interface',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPais()
    {
        return $this->hasMany(Paises::className(), ['id_pais' => 'id_pais']);
    }
}
