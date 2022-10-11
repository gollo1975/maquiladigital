<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "asignacion_producto_detalle".
 *
 * @property int $id_detalle
 * @property int $id_asignacion
 * @property int $id_producto_talla
 * @property int $id_producto
 * @property int $cantidad
 * @property int $valor_minuto
 * @property int $subtotal_producto
 * @property string $fecha_proceso
 * @property string $usuario
 *
 * @property AsignacionProducto $asignacion
 * @property ProductoTalla $productoTalla
 * @property CostoProducto $producto
 */
class AsignacionProductoDetalle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'asignacion_producto_detalle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_asignacion', 'id_producto_talla', 'id_producto', 'cantidad', 'valor_minuto', 'subtotal_producto'], 'integer'],
            [['fecha_proceso'], 'safe'],
            [['usuario'], 'string', 'max' => 15],
            [['id_asignacion'], 'exist', 'skipOnError' => true, 'targetClass' => AsignacionProducto::className(), 'targetAttribute' => ['id_asignacion' => 'id_asignacion']],
            [['id_producto_talla'], 'exist', 'skipOnError' => true, 'targetClass' => ProductoTalla::className(), 'targetAttribute' => ['id_producto_talla' => 'id_producto_talla']],
            [['id_producto'], 'exist', 'skipOnError' => true, 'targetClass' => CostoProducto::className(), 'targetAttribute' => ['id_producto' => 'id_producto']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_detalle' => 'Id Detalle',
            'id_asignacion' => 'Id Asignacion',
            'id_producto_talla' => 'Id Producto Talla',
            'id_producto' => 'Id Producto',
            'cantidad' => 'Cantidad',
            'valor_minuto' => 'Valor Minuto',
            'subtotal_producto' => 'Subtotal Producto',
            'fecha_proceso' => 'Fecha Proceso',
            'usuario' => 'Usuario',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAsignacion()
    {
        return $this->hasOne(AsignacionProducto::className(), ['id_asignacion' => 'id_asignacion']);
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
    public function getProducto()
    {
        return $this->hasOne(CostoProducto::className(), ['id_producto' => 'id_producto']);
    }
}
