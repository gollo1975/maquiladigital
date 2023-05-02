<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "nivel_estudio".
 *
 * @property int $id_nivel_estudio
 * @property string $nive_estudio
 *
 * @property Empleado[] $empleados
 */
class NivelEstudio extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nivel_estudio';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nive_estudio'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_nivel_estudio' => 'Id Nivel Estudio',
            'nive_estudio' => 'NiveL Estudio',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmpleados()
    {
        return $this->hasMany(Empleado::className(), ['id_nivel_estudio' => 'id_nivel_estudio']);
    }
}
