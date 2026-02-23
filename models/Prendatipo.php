<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "prendatipo".
 *
 * @property int $idprendatipo
 * @property string $prenda
 * @property int $idtalla
 *
 * @property Talla $talla
 * @property Producto[] $productos
 */
class Prendatipo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'prendatipo';
    }

    public function beforeSave($insert) {
        if(!parent::beforeSave($insert)){
            return false;
        }
        $this->prenda = strtoupper($this->prenda);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['prenda','idtalla'], 'required', 'message' => 'Campo requerido'],
            [['idtalla'], 'integer'],
            [['prenda'], 'string', 'max' => 40],
            [['idtalla'], 'exist', 'skipOnError' => true, 'targetClass' => Talla::className(), 'targetAttribute' => ['idtalla' => 'idtalla']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'idprendatipo' => 'Id',
            'prenda' => 'Nombre de la prenda',
            'idtalla' => 'Talla',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTalla()
    {
        return $this->hasOne(Talla::className(), ['idtalla' => 'idtalla']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductos()
    {
        return $this->hasMany(Producto::className(), ['idprendatipo' => 'idprendatipo']);
    }

    public function getNombreProducto()
    {
        return "{$this->prenda} - {$this->talla->talla} - {$this->talla->sexo}";
    }
}
