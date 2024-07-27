<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "grupo_insumos".
 *
 * @property int $id_grupo
 * @property string $nombre_grupo
 *
 * @property Insumos[] $insumos
 */
class GrupoInsumos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'grupo_insumos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre_grupo'], 'required'],
            [['nombre_grupo'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_grupo' => 'Id Grupo',
            'nombre_grupo' => 'Nombre Grupo',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInsumos()
    {
        return $this->hasMany(Insumos::className(), ['id_grupo' => 'id_grupo']);
    }
}
