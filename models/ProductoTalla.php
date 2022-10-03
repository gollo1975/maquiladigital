<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "producto_talla".
 *
 * @property int $id
 * @property int $idtalla
 * @property int $id_producto
 * @property int $cantidad
 * @property string $fecha_registro
 * @property string $usuariosistema
 *
 * @property Talla $talla
 * @property CostoProducto $producto
 */
class ProductoTalla extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'producto_talla';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idtalla', 'id_producto', 'cantidad'], 'integer'],
            [['fecha_registro'], 'safe'],
            [['usuariosistema'], 'string', 'max' => 15],
            [['idtalla'], 'exist', 'skipOnError' => true, 'targetClass' => Talla::className(), 'targetAttribute' => ['idtalla' => 'idtalla']],
            [['id_producto'], 'exist', 'skipOnError' => true, 'targetClass' => CostoProducto::className(), 'targetAttribute' => ['id_producto' => 'id_producto']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_producto_talla' => 'Id',
            'idtalla' => 'Talla',
            'id_producto' => 'Producto',
            'cantidad' => 'Cantidad',
            'fecha_registro' => 'Fecha Registro',
            'usuariosistema' => 'Usuario',
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
    public function getProducto()
    {
        return $this->hasOne(CostoProducto::className(), ['id_producto' => 'id_producto']);
    }
}
