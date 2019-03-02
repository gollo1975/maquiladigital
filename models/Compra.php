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
 * @property int $factura
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
            [['id_compra_concepto', 'id_proveedor', 'factura', 'subtotal','fechainicio','fechavencimiento'], 'required'],
            [['id_compra_concepto', 'id_proveedor', 'estado', 'autorizado','factura','numero'], 'integer'],
            [['porcentajeiva', 'porcentajefuente', 'porcentajereteiva', 'subtotal', 'retencionfuente', 'impuestoiva', 'retencioniva', 'saldo', 'base_aiu', 'total'], 'number'],
            [['observacion'], 'string'],
            [['fechacreacion','fechainicio','fechavencimiento'], 'safe'],
            [['usuariosistema'], 'string', 'max' => 50],
            [['id_compra_concepto'], 'exist', 'skipOnError' => true, 'targetClass' => CompraConcepto::className(), 'targetAttribute' => ['id_compra_concepto' => 'id_compra_concepto']],
            [['id_proveedor'], 'exist', 'skipOnError' => true, 'targetClass' => Proveedor::className(), 'targetAttribute' => ['id_proveedor' => 'idproveedor']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_compra' => 'Id Compra',
            'id_compra_concepto' => 'Compra Concepto',
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
            'fechavencimiento' => 'Fecha Vencimiento',
            'factura' => 'Factura',
            'numero' => 'Número',
            'base_aiu' => 'Base AIU',
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
}