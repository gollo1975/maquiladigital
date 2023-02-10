<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tipo_clasificacion_segundas".
 *
 * @property int $id_tipo
 * @property string $concepto
 *
 * @property ClasificacionSegundas[] $clasificacionSegundas
 */
class TipoClasificacionSegundas extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tipo_clasificacion_segundas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['concepto'], 'required'],
            [['concepto'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_tipo' => 'Id Tipo',
            'concepto' => 'Concepto',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClasificacionSegundas()
    {
        return $this->hasMany(ClasificacionSegundas::className(), ['id_tipo' => 'id_tipo']);
    }
}
