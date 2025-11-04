<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "detalle_color_talla".
 *
 * @property int $id_detalle
 * @property int $id_inventario
 * @property int $codigo_producto
 * @property int $id
 * @property int $idtalla
 * @property int $id_punto
 * @property int $cantidad
 * @property int $stock_punto
 * @property int $cerrado
 * @property string $user_name
 * @property string $fecha_registro
 *
 * @property InventarioPuntoVenta $inventario
 * @property Color $id0
 * @property Talla $talla
 * @property PuntoVenta $punto
 */
class DetalleColorTalla extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'detalle_color_talla';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_inventario', 'codigo_producto', 'id', 'idtalla', 'id_punto', 'cantidad', 'stock_punto', 'cerrado'], 'integer'],
            [['fecha_registro'], 'safe'],
            [['user_name'], 'string', 'max' => 15],
            [['id_inventario'], 'exist', 'skipOnError' => true, 'targetClass' => InventarioPuntoVenta::className(), 'targetAttribute' => ['id_inventario' => 'id_inventario']],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => Color::className(), 'targetAttribute' => ['id' => 'id']],
            [['idtalla'], 'exist', 'skipOnError' => true, 'targetClass' => Talla::className(), 'targetAttribute' => ['idtalla' => 'idtalla']],
            [['id_punto'], 'exist', 'skipOnError' => true, 'targetClass' => PuntoVenta::className(), 'targetAttribute' => ['id_punto' => 'id_punto']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_detalle' => 'Id Detalle',
            'id_inventario' => 'Id Inventario',
            'codigo_producto' => 'Codigo Producto',
            'id' => 'ID',
            'idtalla' => 'Idtalla',
            'id_punto' => 'Id Punto',
            'cantidad' => 'Cantidad',
            'stock_punto' => 'Stock Punto',
            'cerrado' => 'Cerrado',
            'user_name' => 'User Name',
            'fecha_registro' => 'Fecha Registro',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInventario()
    {
        return $this->hasOne(InventarioPuntoVenta::className(), ['id_inventario' => 'id_inventario']);
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
    public function getTalla()
    {
        return $this->hasOne(Talla::className(), ['idtalla' => 'idtalla']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPunto()
    {
        return $this->hasOne(PuntoVenta::className(), ['id_punto' => 'id_punto']);
    }
    
    public function getCerradoDetalle(){
        if($this->cerrado == 0){
            $cerradodetalle = 'NO';
        } else {
            $cerradodetalle = 'SI';
        }
        
        return $cerradodetalle;
    }
}
