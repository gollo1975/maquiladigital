<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "costo_producto".
 *
 * @property int $id_producto
 * @property string $codigo_producto
 * @property int $id_tipo_producto
 * @property string $descripcion
 * @property string $fecha_creacion
 * @property int $costo_sin_iva
 * @property int $costo_con_iva
 * @property double $porcentaje_iva
 * @property string $usuariosistema
 *
 * @property TipoProducto $tipoProducto
 * @property CostoProductoDetalle[] $costoProductoDetalles
 */
class CostoProducto extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'costo_producto';
    }
  public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->descripcion = strtoupper($this->descripcion);        
        $this->observacion = strtolower($this->observacion); 
        $this->observacion = ucfirst($this->observacion);  
        return true;
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descripcion','codigo_producto','id_tipo_producto'], 'required'],
            [['id_tipo_producto', 'costo_sin_iva', 'costo_con_iva','autorizado','aplicar_iva','subtotal_producto','total_producto','cantidad',
                'asignado','entregado','despacho_insumo'], 'integer'],
            [['fecha_creacion'], 'safe'],
            [['porcentaje_iva','tiempo_confeccion','tiempo_terminacion'], 'number'],
            ['codigo_producto', 'codigo_existe'],
            [['descripcion'], 'string', 'max' => 45],
            [['codigo_producto'], 'string', 'max' => 10],
            [['observacion'], 'string', 'max' => 80],
            [['usuariosistema'], 'string', 'max' => 20],
            [['id_tipo_producto'], 'exist', 'skipOnError' => true, 'targetClass' => TipoProducto::className(), 'targetAttribute' => ['id_tipo_producto' => 'id_tipo_producto']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_producto' => 'Id Producto',
            'codigo_producto' => 'Codigo Producto:',
            'id_tipo_producto' => 'Tipo producto:',
            'descripcion' => 'Descripción:',
            'fecha_creacion' => 'Fecha Creacion',
            'aplicar_iva' => 'Aplicar iva',
            'costo_sin_iva' => 'Costo Sin Iva',
            'costo_con_iva' => 'Costo Con Iva',
            'porcentaje_iva' => 'Porcentaje Iva',
            'observacion' => 'Observación:',
            'usuariosistema' => 'Usuariosistema',
            'subtotal_producto' => 'Subtotal producto:',
            'total_producto' => 'Total producto:',
            'cantidad' => 'Unidades:',
            'asignado' => 'Asignado:',
            'tiempo_confeccion' => 'Tiempo confeccion:',
            'tiempo_terminacion' => 'Tiempo terminacion:',
            'entregado' => 'entregado',
            'despacho_insumo' => 'Despacho insumo:',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoProducto()
    {
        return $this->hasOne(TipoProducto::className(), ['id_tipo_producto' => 'id_tipo_producto']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCostoProductoDetalles()
    {
        return $this->hasMany(CostoProductoDetalle::className(), ['id_producto' => 'id_producto']);
    }
    
    public function getAutorizadocosto() {
        if($this->autorizado == 1){
            $autorizado = 'SI';
        }else{
            $autorizado = 'NO';
        }
        return $autorizado;
    }
    public function getAsignarProducto() {
        if($this->asignado == 1){
            $asignado = 'SI';
        }else{
            $asignado = 'NO';
        }
        return $asignado;
    }
    public function getAplicaiva() {
        if($this->aplicar_iva == 1){
            $aplica = 'SI';
        }else{
            $aplica = 'NO';
        }
        return $aplica;
    }
    
     public function codigo_existe($attribute, $params)
    {
        //Buscar el codigo en la tabla
        $table = CostoProducto::find()->where("codigo_producto=:codigo_producto", [":codigo_producto" => $this->codigo_producto]);
        //Si el codigo existe en inscritos mostrar el error
        if ($table->count() > 1)
        {
            $this->addError($attribute, "Este Código ya esta creado en sistema, consulte al administrador");
        }
    }
    
    public function getProductos()
    {
        return "{$this->codigo_producto} - {$this->descripcion}";
    }
}
