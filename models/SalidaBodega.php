<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "salida_bodega".
 *
 * @property int $id_salida_bodega
 * @property int $id_producto
 * @property string $codigo_producto
 * @property int $unidades
 * @property string $fecha_salida
 * @property string $responsable
 * @property int $autorizado
 * @property int $proceso_cerrado
 * @property string $user_name
 *
 * @property CostoProducto $producto
 * @property SalidaBodegaDetalle[] $salidaBodegaDetalles
 */
class SalidaBodega extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'salida_bodega';
    }

     public function beforeSave($insert) {
	if(!parent::beforeSave($insert)){
            return false;
        }
	# ToDo: Cambiar a cliente cargada de configuración.    
	$this->responsable= strtoupper($this->responsable);
	
        return true;
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_producto', 'fecha_salida', 'responsable'], 'required'],
            [['id_producto', 'unidades', 'autorizado', 'proceso_cerrado','numero_salida','exportar_inventario'], 'integer'],
            [['fecha_salida'], 'safe'],
            [['codigo_producto', 'user_name'], 'string', 'max' => 15],
            [['responsable'], 'string', 'max' => 40],
            [['observacion'],'string' ,'max' => 100],
            [['id_producto'], 'exist', 'skipOnError' => true, 'targetClass' => CostoProducto::className(), 'targetAttribute' => ['id_producto' => 'id_producto']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_salida_bodega' => 'iD',
            'id_producto' => 'Referencia:',
            'codigo_producto' => 'Codigo Producto',
            'unidades' => 'Unidades',
            'fecha_salida' => 'Fecha salida:',
            'responsable' => 'Responsable:',
            'autorizado' => 'Autorizado:',
            'proceso_cerrado' => 'Proceso cerrado:',
            'user_name' => 'User name:',
            'numero_salida' => 'Numero salida:',
            'exportar_inventario' => 'Exportado:',
            'observacion' => 'Nota:',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducto()
    {
        return $this->hasOne(CostoProducto::className(), ['id_producto' => 'id_producto']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSalidaBodegaDetalles()
    {
        return $this->hasMany(SalidaBodegaDetalle::className(), ['id_salida_bodega' => 'id_salida_bodega']);
    }
    
    public function getAutorizadoSalida() {
        if($this->autorizado == 0){
            $autorizadosalida = 'NO';
        }else{
            $autorizadosalida = 'SI';
        }
        return $autorizadosalida;
    }
   
     public function getCerradoSalida() {
        if($this->proceso_cerrado == 0){
            $cerradosalida = 'NO';
        }else{
            $cerradosalida = 'SI';
        }
        return $cerradosalida;
    }
    
     public function getInsumosExportado() {
        if($this->exportar_inventario == 0){
             $insumosexportado = 'NO';
        }else{
            $insumosexportado = 'SI';
        }
        return $insumosexportado;
    }
}
