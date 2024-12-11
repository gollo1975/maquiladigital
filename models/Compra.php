<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "compra".
 *
 * @property int $id_compra
 * @property int $id_compra_tipo
 * @property double $porcentajeiva
 * @property double $porcentajefuente
 * @property double $porcentajereteiva
 * @property double $subtotal
 * @property double $retencionfuente
 * @property double $impuestoiva
 * @property double $retencioniva
 * @property double $porcentajeaiu
 * @property double $saldo
 * @property double $total
 * @property double $base_aiu
 * @property int $id_proveedor
 * @property string $usuariosistema
 * @property int $estado
 * @property int $autorizado
 * @property string $observacion
 * @property string $fechacreacion
 * @property string $factura
 * @property int $numero
 *
 * @property CompraTipo $compraComcepto
 * @property Proveedor $proveedor
 */
class Compra extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'compra';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_compra_concepto', 'id_proveedor', 'factura', 'subtotal','fechainicio','fechavencimiento','id_tipo_compra'], 'required'],
            [['id_compra_concepto', 'id_proveedor', 'estado', 'autorizado','numero','id_tipo_compra','genera_documento_soporte','documento_generado'], 'integer'],
            [['porcentajeiva', 'porcentajefuente', 'porcentajereteiva', 'subtotal', 'retencionfuente', 'impuestoiva', 'retencioniva', 'saldo', 'base_aiu', 'total'], 'number'],
            [['observacion','factura'], 'string'],
            [['fechacreacion','fechainicio','fechavencimiento'], 'safe'],
            [['usuariosistema'], 'string', 'max' => 50],
            [['id_compra_concepto'], 'exist', 'skipOnError' => true, 'targetClass' => CompraConcepto::className(), 'targetAttribute' => ['id_compra_concepto' => 'id_compra_concepto']],
            [['id_proveedor'], 'exist', 'skipOnError' => true, 'targetClass' => Proveedor::className(), 'targetAttribute' => ['id_proveedor' => 'idproveedor']],
            [['id_tipo_compra'], 'exist', 'skipOnError' => true, 'targetClass' => TipoCompraProceso::className(), 'targetAttribute' => ['id_tipo_compra' => 'id_tipo_compra']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_compra' => 'Id Compra',
            'id_compra_concepto' => 'Concepto',
            'porcentajeiva' => 'Porcentaje Iva',
            'porcentajefuente' => 'Porcentaje Fuente',
            'porcentajereteiva' => 'Porcentaje Reteiva',
            'porcentajereteaiu' => 'Porcentaje Aiu',
            'subtotal' => 'Subtotal',
            'retencionfuente' => 'Retencion Fuente',
            'impuestoiva' => 'Impuesto Iva',
            'retencioniva' => 'Retencion Iva',
            'saldo' => 'Saldo',
            'total' => 'Total',
            'id_proveedor' => 'Proveedor',
            'usuariosistema' => 'Usuario',
            'estado' => 'Estado',
            'autorizado' => 'Autorizado',
            'observacion' => 'Observacion',
            'fechacreacion' => 'Fecha Creacion',
            'fechainicio' => 'Fecha Inicio',
            'fechavencimiento' => 'Fecha Vcto',
            'factura' => 'Factura',
            'numero' => 'Número',
            'base_aiu' => 'Base AIU',
            'id_tipo_compra' => 'Tipo compra',
            'genera_documento_soporte' => 'Genera documento soporte:',
            'documento_generado' => 'documento_generado',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompraConcepto()
    {
        return $this->hasOne(CompraConcepto::className(), ['id_compra_concepto' => 'id_compra_concepto']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProveedor()
    {
        return $this->hasOne(Proveedor::className(), ['idproveedor' => 'id_proveedor']);
    }
    
    public function getTipoCompra() {
        return $this->hasOne(TipoCompraProceso::className(), ['id_tipo_compra' => 'id_tipo_compra']); 
               
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
        return $estado;
    }
    
    //proceso para llamar varios campos
    public function getCompras()
    {
        return "Cuenta cobro: {$this->factura} - Valor: {$this->total}";
    }
}
