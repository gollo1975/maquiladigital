<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "categoria".
 *
 * @property int $id_categoria
 * @property string $categoria
 *
 * @property InventarioPuntoVenta[] $inventarioPuntoVentas
 */
class Categoria extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'categoria';
    }

    public function beforeSave($insert) {
	if(!parent::beforeSave($insert)){
            return false;
        }
	# ToDo: Cambiar a cliente cargada de configuración.    
	$this->categoria = strtoupper($this->categoria);
	
        return true;
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['categoria'], 'required'],
            [['categoria'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_categoria' => 'Codigo',
            'categoria' => 'Nombre de la categoria',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInventarioPuntoVentas()
    {
        return $this->hasMany(InventarioPuntoVenta::className(), ['id_categoria' => 'id_categoria']);
    }
}
