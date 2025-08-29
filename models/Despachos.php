<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "despachos".
 *
 * @property int $id_despacho
 * @property int $id_salida
 * @property int $idproveedor
 * @property int $nombre_proveedor
 * @property string $codigo_producto
 * @property string $ciudad_origen
 * @property string $ciudad_destino
 * @property int $total_tulas
 * @property int $valor_flete
 * @property string $fecha_despacho
 * @property string $fecha_registro
 * @property string $user_name
 *
 * @property SalidaEntradaProduccion $salida
 * @property Proveedor $proveedor
 */
class Despachos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'despachos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_salida', 'idproveedor', 'total_tulas', 'valor_flete','id_entrada_tipo','autorizado','numero_despacho',
                'proceso_cerrado','tulas_reales'], 'integer'],
            [['valor_flete','id_entrada_tipo','ciudad_origen','ciudad_destino','fecha_despacho','idproveedor','id_salida'], 'required'],
            [['fecha_despacho', 'fecha_registro'], 'safe'],
            [['observacion'],'string', 'max' => 150],
            [['municipio_origen','municipio_destino','nombre_proveedor'],'string', 'max' => 40],
            [['codigo_producto', 'ciudad_origen', 'ciudad_destino', 'user_name'], 'string', 'max' => 15],
            [['id_salida'], 'exist', 'skipOnError' => true, 'targetClass' => SalidaEntradaProduccion::className(), 'targetAttribute' => ['id_salida' => 'id_salida']],
            [['idproveedor'], 'exist', 'skipOnError' => true, 'targetClass' => Proveedor::className(), 'targetAttribute' => ['idproveedor' => 'idproveedor']],
            [['id_entrada_tipo'], 'exist', 'skipOnError' => true, 'targetClass' => TipoEntrada::className(), 'targetAttribute' => ['id_entrada_tipo' => 'id_entrada_tipo']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_despacho' => 'Id',
            'id_salida' => 'Salida produccion:',
            'idproveedor' => 'Proveedor:',
            'nombre_proveedor' => 'Nombre Proveedor',
            'codigo_producto' => 'Codigo Producto',
            'ciudad_origen' => 'Ciudad origen:',
            'ciudad_destino' => 'Ciudad destino:',
            'total_tulas' => 'Total tulas:',
            'valor_flete' => 'Valor flete:',
            'fecha_despacho' => 'Fecha despacho:',
            'fecha_registro' => 'Fecha Registro',
            'user_name' => 'User Name',
            'id_entrada_tipo' => 'Tipo despacho:',
            'autorizado' => 'autorizado',
            'numero_despacho' => 'numero despacho:',
            'proceso_cerrado' => 'Cerrado:',
            'observacion' => 'Observacion:',
            'municipio_origen' => 'Municipio origen:',
            'municipio_destino' => 'Municipio destino:',
            'tulas_reales' => 'tulas_reales',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSalida()
    {
        return $this->hasOne(SalidaEntradaProduccion::className(), ['id_salida' => 'id_salida']);
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
    public function getTipoEntrada()
    {
        return $this->hasOne(TipoEntrada::className(), ['id_entrada_tipo' => 'id_entrada_tipo']);
    }
    
    public function getProcesoCerrado() {
        if($this->proceso_cerrado == 0){
            $procesocerrado = 'NO';
        }else{
            $procesocerrado = 'SI';
        }
        return $procesocerrado;
    }
    
    public function getAutorizadoRegistro() {
        if($this->autorizado == 0){
            $autorizadoregistro = 'NO';
        }else{
            $autorizadoregistro = 'SI';
        }
        return $autorizadoregistro;
    }
}
