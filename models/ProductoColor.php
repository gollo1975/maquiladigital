<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "producto_color".
 *
 * @property int $id_producto_color
 * @property int $id_producto_talla
 * @property int $id
 * @property int $id_producto
 * @property int $cantidad_color
 * @property string $usuariosistema
 * @property string $fecha_registro
 *
 * @property ProductoTalla $productoTalla
 * @property Color $id0
 * @property CostoProducto $producto
 */
class ProductoColor extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'producto_color';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_producto_talla', 'id', 'id_producto', 'cantidad_color'], 'integer'],
            [['fecha_registro'], 'safe'],
            [['usuariosistema'], 'string', 'max' => 15],
            [['id_producto_talla'], 'exist', 'skipOnError' => true, 'targetClass' => ProductoTalla::className(), 'targetAttribute' => ['id_producto_talla' => 'id_producto_talla']],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => Color::className(), 'targetAttribute' => ['id' => 'id']],
            [['id_producto'], 'exist', 'skipOnError' => true, 'targetClass' => CostoProducto::className(), 'targetAttribute' => ['id_producto' => 'id_producto']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_producto_color' => 'Id Producto Color',
            'id_producto_talla' => 'Id Producto Talla',
            'id' => 'ID',
            'id_producto' => 'Id Producto',
            'cantidad_color' => 'Cantidad Color',
            'usuariosistema' => 'Usuariosistema',
            'fecha_registro' => 'Fecha Registro',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductoTalla()
    {
        return $this->hasOne(ProductoTalla::className(), ['id_producto_talla' => 'id_producto_talla']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getColor()
    {
        return $this->hasOne(Color::className(), ['id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducto()
    {
        return $this->hasOne(CostoProducto::className(), ['id_producto' => 'id_producto']);
    }
}
