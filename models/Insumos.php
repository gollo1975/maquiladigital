<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "insumos".
 *
 * @property int $id_insumos
 * @property string $codigo_insumo
 * @property string $descripcion
 * @property string $fecha_entrada
 * @property int $estado_insumo
 * @property string $usuariosistema
 */
class Insumos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'insumos';
    }
    public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->codigo_insumo = strtoupper($this->codigo_insumo);        
        $this->descripcion = strtoupper($this->descripcion);        
        return true;
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['codigo_insumo', 'descripcion', 'estado_insumo','id_tipo_medida','precio_unitario','fecha_entrada', 'aplica_iva', 'id_impuesto', 'stock_inicial',
                'aplica_inventario','inventario_inicial','idproveedor','id_grupo'], 'required'],
            [['fecha_entrada','fecha_registro','fecha_vencimiento'], 'safe'],
            [['estado_insumo','stock_inicial','aplica_inventario','inventario_inicial','subtotal','total_iva','total_materia_prima','aplica_iva','id_impuesto','stock_real',
                'id_tipo_medida','idproveedor','id_grupo'], 'integer'],
             [['precio_unitario','porcentaje_iva'], 'number'],
            [['codigo_insumo','codigo_ean'], 'string', 'max' => 15],
            [['descripcion'], 'string', 'max' => 60],
            [['observacion'], 'string', 'max' => 100],
            [['usuariosistema'], 'string', 'max' => 20],
            [['id_tipo_medida'], 'exist', 'skipOnError' => true, 'targetClass' => TipoMedida::className(), 'targetAttribute' => ['id_tipo_medida' => 'id_tipo_medida']],
            [['id_impuesto'], 'exist', 'skipOnError' => true, 'targetClass' => Impuestos::className(), 'targetAttribute' => ['id_impuesto' => 'id_impuesto']],
            [['idproveedor'], 'exist', 'skipOnError' => true, 'targetClass' => Proveedor::className(), 'targetAttribute' => ['idproveedor' => 'idproveedor']],
            [['id_grupo'], 'exist', 'skipOnError' => true, 'targetClass' => GrupoInsumos::className(), 'targetAttribute' => ['id_grupo' => 'id_grupo']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_insumos' => 'Id',
            'codigo_insumo' => 'Código:',
            'descripcion' => 'Materia prima:',
            'id_tipo_medida' => 'Tipo medida',
            'precio_unitario' => 'Precio unitario:',
            'fecha_entrada' => 'Fecha Entrada',
            'fecha_vencimiento' => 'Fecha_vencimiento:',
            'aplica_iva' => 'Aplica iva:',
            'id_impuesto' => 'Impuesto:',
            'stock_inicial' => 'Stock inicial:',
            'stock_real' => 'Stock real:',
            'aplica_inventario' => 'Aplica inventario:',
            'inventario_inicial' => 'Inventario inicial:',
            'estado_insumo' => 'Activo:',
            'usuariosistema' => 'User name:',
            'observacion' => 'Observacion:',
            'idproveedor' => 'Proveedor:',
            'id_grupo' => 'Grupo:',
            
        ];
    }
    
    public function getEstado()
    {
        if($this->estado_insumo == 0){
            $estado = "SI";
        }else{
            $estado = "NO";
        }
        return $estado;
    }
    //aplica inventario
    public function getAplicaInventario()
    {
        if($this->aplica_inventario == 0){
            $aplicainventario = "NO";
        }else{
            $aplicainventario = "SI";
        }
        return $aplicainventario;
    }
    
    //inventario inicial
    public function getInventarioInicial()
    {
        if($this->inventario_inicial == 0){
            $inventarioinicial = "NO";
        }else{
            $inventarioinicial = "SI";
        }
        return $inventarioinicial;
    }
    
    //aplica iva
    public function getAplicaIva()
    {
        if($this->aplica_iva == 0){
            $aplicaiva = "NO";
        }else{
            $aplicaiva = "SI";
        }
        return $aplicaiva;
    }
    
    public function getTipomedida()
    {
        return $this->hasOne(TipoMedida::className(), ['id_tipo_medida' => 'id_tipo_medida']);
    }
    
    public function getProveedorMateria()
    {
        return $this->hasOne(Proveedor::className(), ['idproveedor' => 'idproveedor']);
    }
    
    public function getImpuesto()
    {
        return $this->hasOne(Impuestos::className(), ['id_impuesto' => 'id_impuesto']);
    }
    
      public function getGrupo()
    {
        return $this->hasOne(GrupoInsumos::className(), ['id_grupo' => 'id_grupo']);
    }
}
