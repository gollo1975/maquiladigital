<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "notacredito".
 *
 * @property int $idnotacredito
 * @property int $idcliente
 * @property string $fecha
 * @property string $fechapago
 * @property int $idconceptonota
 * @property double $valor
 * @property double $iva
 * @property double $reteiva
 * @property double $retefuente
 * @property double $total
 * @property int $numero
 * @property int $autorizado
 * @property int $anulado
 * @property string $usuariosistema
 * @property string $observacion
 *
 * @property Cliente $cliente
 * @property Conceptonota $conceptonota
 */
class Notacredito extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notacredito';
    }
    
    public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->observacion = strtoupper($this->observacion);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idcliente', 'id_concepto','id_documento'], 'required'],
            [['idcliente', 'id_documento', 'numero', 'autorizado', 'anulado','id_concepto','id_detalle_factura_api'], 'integer'],
            [['fecha', 'fechapago','fecha_recepcion_dian','fecha_envio_api','fecha_factura_venta'], 'safe'],
            [['valor','iva','reteiva','retefuente','total'], 'number'],
            [['observacion','cufe','cude','qrstr'], 'string'],
            [['usuariosistema'], 'string', 'max' => 50],
            [['idcliente'], 'exist', 'skipOnError' => true, 'targetClass' => Cliente::className(), 'targetAttribute' => ['idcliente' => 'idcliente']],
            [['id_documento'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentoElectronico::className(), 'targetAttribute' => ['id_documento' => 'id_documento']],
            [['id_concepto'], 'exist', 'skipOnError' => true, 'targetClass' => ConceptoNotaCreditoDevolucion::className(), 'targetAttribute' => ['id_concepto' => 'id_concepto']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'idnotacredito' => 'Id',
            'idcliente' => 'Cliente',
            'fecha' => 'Fecha/hora',
            'fechapago' => 'Fecha creacion',
            'id_documento' => 'Tipo de documento',
            'valor' => 'Valor',
            'iva' => 'Iva',
            'reteiva' => 'Rete Iva',
            'retefuente' => 'Rete Fuente',
            'total' => 'Total pagar',
            'numero' => 'Numero',
            'autorizado' => 'Aut.',
            'anulado' => 'Anulado',
            'usuariosistema' => 'User name',
            'observacion' => 'Observacion',
            'id_concepto' => 'Motivo',
            'fecha_envio_api' => 'fecha_envio_api',
            'fecha_recepcion_dian'=> 'fecha_recepcion_dian',
            'cufe' => 'Cufe',
            'cude' => 'Cude',
            'id_detalle_factura_api' => 'id_detalle_factura_api',
            'fecha_factura_venta' => 'Fecha venta factura',
            
            
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
    public function getMotivoNota()
    {
        return $this->hasOne(ConceptoNotaCreditoDevolucion::className(), ['id_concepto' => 'id_concepto']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentoeletronico()
    {
        return $this->hasOne(DocumentoElectronico::className(), ['id_documento' => 'id_documento']);
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
}
