<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "facturaventa".
 *
 * @property int $idfactura
 * @property int $nrofactura
 * @property string $fechainicio
 * @property string $fechavcto
 * @property string $fechacreacion
 * @property string $formapago
 * @property int $plazopago
 * @property double $porcentajeiva
 * @property double $porcentajefuente
 * @property double $porcentajereteiva
 * @property double $subtotal
 * @property double $retencionfuente
 * @property double $impuestoiva
 * @property double $retencioniva
 * @property double $saldo
 * @property double $totalpagar
 * @property string $valorletras
 * @property int $idcliente
 * @property int $observacion
 * @property int $idordenproduccion
 * @property string $usuariosistema
 * @property int $idresolucion
 * @property int $estado
 * @property int $autorizado
 * @property int $id_factura_venta_tipo
 * @property int $nrofacturaelectronica
 * 
 * @property Cliente $cliente
 * @property Ordenproduccion $ordenproduccion
 * @property Resolucion $resolucion
 * @property Facturaventatipo $facturaventatipo
 * @property Facturaventadetalle[] $facturaventadetalles
 * @property Recibocajadetalle[] $recibocajadetalles
 */
class Facturaventa extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'facturaventa';
    }
    
    public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->observacion = strtoupper($this->observacion);
         $this->nrofacturaelectronica = strtoupper($this->nrofacturaelectronica);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nrofactura', 'plazopago', 'idcliente', 'idordenproduccion', 'idresolucion','estado','autorizado','reenviar_factura','consultar_factura'], 'integer'],
            [['fecha_inicio', 'idcliente', 'idordenproduccion','id_factura_venta_tipo'], 'required', 'message' => 'Campo requerido'],
            [['fecha_inicio', 'fecha_vencimiento', 'fechacreacion','fecha_envio_dian'], 'safe'],
            [['porcentajeiva', 'porcentajefuente', 'porcentajereteiva', 'subtotal', 'retencionfuente', 'impuestoiva', 'retencioniva', 'saldo', 'totalpagar'], 'number'],
            [['valorletras','observacion','nrofacturaelectronica','cufe','numero_resolucion'], 'string'],
            [['id_forma_pago', 'usuariosistema','consecutivo'], 'string', 'max' => 15],
            [['idcliente'], 'exist', 'skipOnError' => true, 'targetClass' => Cliente::className(), 'targetAttribute' => ['idcliente' => 'idcliente']],
            [['idordenproduccion'], 'exist', 'skipOnError' => true, 'targetClass' => Ordenproduccion::className(), 'targetAttribute' => ['idordenproduccion' => 'idordenproduccion']],
            [['idresolucion'], 'exist', 'skipOnError' => true, 'targetClass' => Resolucion::className(), 'targetAttribute' => ['idresolucion' => 'idresolucion']],
            [['id_forma_pago'], 'exist', 'skipOnError' => true, 'targetClass' => FormaPago::className(), 'targetAttribute' => ['id_forma_pago' => 'id_forma_pago']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'idfactura' => 'ID',
            'nrofactura' => 'Nro Factura',
            'fecha_inicio' => 'Fecha Inicio factura',
            'fecha_vencimiento' => 'Fecha Vcto',
            'fechacreacion' => 'Fecha Creacion',
            'id_forma_pago' => 'Forma de pago',
            'plazopago' => 'Plazo pago',
            'porcentajeiva' => '% Iva',
            'porcentajefuente' => '% Rete Fuente',
            'porcentajereteiva' => '% Rete Iva',
            'subtotal' => 'Subtotal',
            'retencionfuente' => 'Retencion Fuente',
            'impuestoiva' => 'Iva',
            'retencioniva' => 'Retencion Iva',
            'saldo' => 'Saldo',
            'totalpagar' => 'Total Pagar',
            'valorletras' => 'Valor Letras',
            'idcliente' => 'Nombre del cliente',
            'idordenproduccion' => 'Orden de produccion',
            'usuariosistema' => 'Usuario Sistema',
            'idresolucion' => 'Resolucion',
            'estado' => 'Estado',
            'autorizado' => 'Autorizado',
            'observacion' => 'Observaciones',
            'id_factura_venta_tipo' => 'Tipo de factura',
            'nrofacturaelectronica' => 'Factura Electrónica',
            'fecha_envio_dian' => 'fecha_envio_dian',
            'numero_resolucion' => 'numero_resolucion',
            'cufe' => 'cufe',
            'consecutivo' => 'consecutivo',
            'consultar_factura' => 'consultar_factura',
            'reenviar_factura' => 'reenviar_factura',
        ];
    }

    public static function getOrden($provid)
    {

        $data=  \app\models\Ordenproduccion::find()
            ->where(['idcliente'=>$provid])
            ->select(['idordenproduccion as id'])->asArray()->all();

        return $data;
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
    public function getFormaPago()
    {
        return $this->hasOne(FormaPago::className(), ['id_forma_pago' => 'id_forma_pago']);
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
    public function getResolucion()
    {
        return $this->hasOne(Resolucion::className(), ['idresolucion' => 'idresolucion']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFacturaventatipo()
    {
        return $this->hasOne(Facturaventatipo::className(), ['id_factura_venta_tipo' => 'id_factura_venta_tipo']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFacturaventadetalles()
    {
        return $this->hasMany(Facturaventadetalle::className(), ['idfactura' => 'idfactura']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecibocajadetalles()
    {
        return $this->hasMany(Recibocajadetalle::className(), ['idfactura' => 'idfactura']);
    }

     
    public function getAutorizar()
    {
        if($this->autorizado == 1){
            $autorizar = "SI";
        }else{
            $autorizar = "NO";
        }
        return $autorizar;
    }
    
    public function getEstados()
    {
        if($this->estado == 0){
            $estado = "ABIERTA";
        }
        if($this->estado == 1){
            $estado = "ABONADA";
        }
        if($this->estado == 2){
            $estado = "PAGADA";
        }
        if($this->estado == 3){
            $estado = "ANULADA NC";
        }
        if($this->estado == 4){
            $estado = "ABONO NC";
        }
        return $estado;
    }
    
}
