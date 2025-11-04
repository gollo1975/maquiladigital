<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "marca".
 *
 * @property int $id_macar
 * @property string $marca
 * @property int $estado
 *
 * @property InventarioPuntoVenta[] $inventarioPuntoVentas
 */
class Marca extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'marca';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['marca'], 'required'],
            [['estado'], 'integer'],
            [['marca'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_marca' => 'Id',
            'marca' => 'Marca',
            'estado' => 'Estado',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInventarioPuntoVentas()
    {
        return $this->hasMany(InventarioPuntoVenta::className(), ['id_marca' => 'id_marca']);
    }
}
