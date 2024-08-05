<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "salida_entrada_produccion".
 *
 * @property int $id_salida
 * @property int $idcliente
 * @property int $idordenproduccion
 * @property string $codigo_producto
 * @property int $tipo_proceso
 * @property string $fecha_entrada_salida
 * @property int $total_cantidad
 * @property string $usuariosistema
 * @property string $fecha_proceso
 * @property string $observacion
 *
 * @property Cliente $cliente
 * @property Ordenproduccion $ordenproduccion
 * @property SalidaEntradaProduccionDetalle[] $salidaEntradaProduccionDetalles
 */
class SalidaEntradaProduccion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'salida_entrada_produccion';
    }
 public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
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
            [['idcliente', 'idordenproduccion', 'tipo_proceso', 'fecha_entrada_salida','id_entrada_tipo'], 'required'],
            [['idcliente', 'idordenproduccion', 'tipo_proceso', 'total_cantidad','numero_tulas','id_entrada_tipo','servicio_cobrado'], 'integer'],
            [['fecha_entrada_salida', 'fecha_proceso'], 'safe'],
            [['codigo_producto'], 'string', 'max' => 15],
            [['usuariosistema'], 'string', 'max' => 20],
            [['observacion'], 'string', 'max' => 100],
            [['idcliente'], 'exist', 'skipOnError' => true, 'targetClass' => Cliente::className(), 'targetAttribute' => ['idcliente' => 'idcliente']],
            [['idordenproduccion'], 'exist', 'skipOnError' => true, 'targetClass' => Ordenproduccion::className(), 'targetAttribute' => ['idordenproduccion' => 'idordenproduccion']],
             [['id_entrada_tipo'], 'exist', 'skipOnError' => true, 'targetClass' => TipoEntrada::className(), 'targetAttribute' => ['id_entrada_tipo' => 'id_entrada_tipo']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_salida' => 'Id',
            'idcliente' => 'Cliente:',
            'idordenproduccion' => 'Op Cliente:',
            'codigo_producto' => 'Codigo Producto:',
            'tipo_proceso' => 'Tipo Proceso:',
            'fecha_entrada_salida' => 'Fecha Entrada Salida:',
            'total_cantidad' => 'Total Cantidad:',
            'usuariosistema' => 'Usuario:',
            'fecha_proceso' => 'Fecha Proceso:',
            'observacion' => 'Observacion:',
            'numero_tulas'=> 'Nro tulas:',
            'id_entrada_tipo' => 'Tipo entrada:',
            'servicio_cobrado' => 'servicio_cobrado',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCliente()
    {
        return $this->hasOne(Cliente::className(), ['idcliente' => 'idcliente']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdenproduccion()
    {
        return $this->hasOne(Ordenproduccion::className(), ['idordenproduccion' => 'idordenproduccion']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSalidaEntradaProduccionDetalles()
    {
        return $this->hasMany(SalidaEntradaProduccionDetalle::className(), ['id_salida' => 'id_salida']);
    }
    
    public function getTipoentrada()
    {
        return $this->hasOne(TipoEntrada::className(), ['id_entrada_tipo' => 'id_entrada_tipo']);
    }
    
    //PROCESO QEU AGRUPA EL CODGIO DE LA REFERENCIA
    public function getNombreReferencia()
    {
        return "Refe: {$this->codigo_producto} - Orden: {$this->idordenproduccion} - Fecha salida: {$this->fecha_entrada_salida}";
    }
    
    public function getTipoProceso()
    {
        if($this->tipo_proceso == 1){
            $tiproceso = 'ENTRADA';
        }else{
            $tiproceso = 'SALIDA';
        }
        return $tiproceso;
    } 
    public function getAutorizadoSalida()
    {
        if($this->autorizado == 1){
            $autorizadosalida = 'SI';
        }else{
            $autorizadosalida = 'NO';
        }
        return $autorizadosalida;
    } 
    
}
