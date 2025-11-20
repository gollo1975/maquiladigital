<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "inventario_punto_venta".
 *
 * @property int $id_inventario
 * @property int $codigo_producto
 * @property string $nombre_producto
 * @property string $descripcion_producto
 * @property int $costo_unitario
 * @property int $stock_unidades
 * @property int $stock_inventario
 * @property int $idproveedor
 * @property int $id_punto
 * @property int $id_marca
 * @property int $id_categoria
 * @property int $iva_incluido
 * @property int $inventario_inicial
 * @property int $aplica_talla_color
 * @property int $aplica_inventario
 * @property double $porcentaje_iva
 * @property int $subtotal
 * @property int $valor_iva
 * @property int $total_inventario
 * @property int $precio_deptal
 * @property int $precio_mayorista
 * @property string $fecha_creacion
 * @property string $fecha_proceso
 * @property string $user_name
 * @property int $codigo_barra
 * @property int $venta_publico
 * @property int $aplica_descuento_punto
 * @property int $aplica_descuento_distribuidor
 * @property int $codigo_enlace_bodega
 * @property int $inventario_aprobado
 * @property int $stock_minimo
 *
 * @property Proveedor $proveedor
 * @property PuntoVenta $punto
 * @property Marca $marca
 * @property Categoria $categoria
 */
class InventarioPuntoVenta extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inventario_punto_venta';
    }
    
     public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
     
        $this->nombre_producto = strtoupper($this->nombre_producto); 
        $this->descripcion_producto = strtoupper($this->descripcion_producto); 
 
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['codigo_producto','nombre_producto','idproveedor','id_marca','id_categoria'], 'required'],
            [['codigo_producto', 'costo_unitario', 'stock_unidades', 'stock_inventario', 'idproveedor', 'id_punto', 'id_marca', 'id_categoria', 'iva_incluido', 'inventario_inicial', 'aplica_talla_color', 'aplica_inventario', 'subtotal', 'valor_iva', 'total_inventario', 'precio_deptal', 'precio_mayorista', 'codigo_barra', 'venta_publico', 'aplica_descuento_punto',
                'aplica_descuento_distribuidor', 'codigo_enlace_bodega', 'inventario_aprobado', 'stock_minimo','idordenproduccion','stock_salida'], 'integer'],
            [['porcentaje_iva'], 'number'],
            [['fecha_creacion', 'fecha_proceso'], 'safe'],
            [['nombre_producto'], 'string', 'max' => 40],
            [['descripcion_producto'], 'string', 'max' => 100],
            [['user_name'], 'string', 'max' => 15],
            [['idproveedor'], 'exist', 'skipOnError' => true, 'targetClass' => Proveedor::className(), 'targetAttribute' => ['idproveedor' => 'idproveedor']],
            [['id_punto'], 'exist', 'skipOnError' => true, 'targetClass' => PuntoVenta::className(), 'targetAttribute' => ['id_punto' => 'id_punto']],
            [['id_marca'], 'exist', 'skipOnError' => true, 'targetClass' => Marca::className(), 'targetAttribute' => ['id_marca' => 'id_marca']],
            [['id_categoria'], 'exist', 'skipOnError' => true, 'targetClass' => Categoria::className(), 'targetAttribute' => ['id_categoria' => 'id_categoria']],
            [['idordenproduccion'], 'exist', 'skipOnError' => true, 'targetClass' => Ordenproduccion::className(), 'targetAttribute' => ['idordenproduccion' => 'idordenproduccion']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_inventario' => 'Id',
            'codigo_producto' => 'Codigo:',
            'nombre_producto' => 'Nombre del producto:',
            'descripcion_producto' => 'Descripcion del producto:',
            'costo_unitario' => 'Costo unitario:',
            'stock_unidades' => 'Stock:',
            'stock_inventario' => 'Stock Inventario',
            'idproveedor' => 'Proveedor:',
            'id_punto' => 'Bodega:',
            'id_marca' => 'Marca:',
            'id_categoria' => 'Categoria:',
            'iva_incluido' => 'Iva incluido:',
            'inventario_inicial' => 'Inventario Inicial:',
            'aplica_talla_color' => 'Aplica talla/Color:',
            'aplica_inventario' => 'Aplica inventario:',
            'porcentaje_iva' => 'Porcentaje',
            'subtotal' => 'Subtotal',
            'valor_iva' => 'Valor iva:',
            'total_inventario' => 'Total Inventario',
            'precio_deptal' => 'Precio Deptal',
            'precio_mayorista' => 'Precio Mayorista',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_proceso' => 'Fecha Proceso',
            'user_name' => 'User Name',
            'codigo_barra' => 'Codigo Barra',
            'venta_publico' => 'Venta a publico:',
            'aplica_descuento_punto' => 'Aplica Descuento Punto',
            'aplica_descuento_distribuidor' => 'Aplica Descuento Distribuidor',
            'codigo_enlace_bodega' => 'Codigo Enlace Bodega',
            'inventario_aprobado' => 'Inventario Aprobado',
            'stock_minimo' => 'Stock Minimo',
            'idordenproduccion' => 'idordenproduccion',
            'stock_salida' => 'stock_salida',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProveedor()
    {
        return $this->hasOne(Proveedor::className(), ['idproveedor' => 'idproveedor']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPunto()
    {
        return $this->hasOne(PuntoVenta::className(), ['id_punto' => 'id_punto']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMarca()
    {
        return $this->hasOne(Marca::className(), ['id_marca' => 'id_marca']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategoria()
    {
        return $this->hasOne(Categoria::className(), ['id_categoria' => 'id_categoria']);
    }
    
     /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdenProduccionInventario()
    {
        return $this->hasOne(Ordenproduccion::className(), ['idordenproduccion' => 'idordenproduccion']);
    }
    
    public function getIvaIncluido() {
        if($this->iva_incluido == 0){
            $ivaincluido = 'NO';
        }else{
            $ivaincluido  = 'SI';
        }
        return $ivaincluido;
    }
    public function getInventarioInicial() {
        if($this->inventario_inicial == 0){
            $inventarioinicial = 'NO';
        }else{
            $inventarioinicial  = 'SI';
        }
        return $inventarioinicial;
    }
    public function getAplicaInventario() {
        if($this->aplica_inventario == 0){
            $aplicainventario = 'NO';
        }else{
            $aplicainventario  = 'SI';
        }
        return $aplicainventario;
    }
    
     public function getVentaPublico() {
        if($this->venta_publico == 0){
            $ventapublico = 'NO';
        }else{
            $ventapublico  = 'SI';
        }
        return $ventapublico;
    }
    
    public function getAplicaDescuentoPunto() {
        if($this->aplica_descuento_punto == 0){
            $aplicadescuentopunto = 'NO';
        }else{
            $aplicadescuentopunto  = 'SI';
        }
        return $aplicadescuentopunto;
    }
    
    public function getAplicaDescuentoDistribuidor() {
        if($this->aplica_descuento_distribuidor == 0){
            $aplicadescuentodistribuidor = 'NO';
        }else{
            $aplicadescuentodistribuidor  = 'SI';
        }
        return $aplicadescuentodistribuidor;
    }
    
    //proceso que incrita varios valores
     public function getInventario()
    {
        return "{$this->codigo_producto} - {$this->nombre_producto}";
    }
    
    public function getAplicaTallaColor() {
        if($this->aplica_talla_color == 0){
            $aplicatallacolor = 'NO';
        }else{
            $aplicatallacolor  = 'SI';
        }
        return $aplicatallacolor;
    }
}
