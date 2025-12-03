<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "packing_pedido_detalle".
 *
 * @property int $id_detalle
 * @property int $id_packing
 * @property int $id_inventario
 * @property int $id
 * @property int $idtalla
 * @property int $codigo
 * @property int $numero_caja
 * @property int $cantidad_despachada
 * @property string $numero_guia
 * @property int $linea_duplicada
 *
 * @property PackingPedido $packing
 * @property InventarioPuntoVenta $inventario
 * @property Color $id0
 * @property Talla $talla
 * @property DespachoPedidoDetalles $codigo0
 */
class PackingPedidoDetalle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'packing_pedido_detalle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_packing', 'id_inventario', 'id', 'idtalla', 'codigo', 'numero_caja', 'cantidad_despachada', 'linea_duplicada','cerrar_linea'], 'integer'],
            [['numero_guia'], 'string', 'max' => 20],
            [['id_packing'], 'exist', 'skipOnError' => true, 'targetClass' => PackingPedido::className(), 'targetAttribute' => ['id_packing' => 'id_packing']],
            [['id_inventario'], 'exist', 'skipOnError' => true, 'targetClass' => InventarioPuntoVenta::className(), 'targetAttribute' => ['id_inventario' => 'id_inventario']],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => Color::className(), 'targetAttribute' => ['id' => 'id']],
            [['idtalla'], 'exist', 'skipOnError' => true, 'targetClass' => Talla::className(), 'targetAttribute' => ['idtalla' => 'idtalla']],
            [['codigo'], 'exist', 'skipOnError' => true, 'targetClass' => DespachoPedidoDetalles::className(), 'targetAttribute' => ['codigo' => 'codigo']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_detalle' => 'Id Detalle',
            'id_packing' => 'Id Packing',
            'id_inventario' => 'Id Inventario',
            'id' => 'ID',
            'idtalla' => 'Idtalla',
            'codigo' => 'Codigo',
            'numero_caja' => 'Numero Caja',
            'cantidad_despachada' => 'Cantidad Despachada',
            'numero_guia' => 'Numero Guia',
            'linea_duplicada' => 'Linea Duplicada',
            'cerrar_linea' => 'Cerrar linea'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPacking()
    {
        return $this->hasOne(PackingPedido::className(), ['id_packing' => 'id_packing']);
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
    public function getColores()
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
    public function getCodigo()
    {
        return $this->hasOne(DespachoPedidoDetalles::className(), ['codigo' => 'codigo']);
    }
    
    public function getCerrarLinea(){
       if($this->cerrar_linea == 0){
           $cerrarlinea = 'NO';
       } else{
           $cerrarlinea = 'SI';
       }
       return $cerrarlinea;
    }
}
