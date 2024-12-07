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
            [['idcliente', 'idconceptonota','id_concepto'], 'required'],
            [['idcliente', 'idconceptonota', 'numero', 'autorizado', 'anulado','id_concepto'], 'integer'],
            [['fecha', 'fechapago','fecha_recepcion_dian','fecha_envio_api'], 'safe'],
            [['valor','iva','reteiva','retefuente','total'], 'number'],
            [['observacion','cufe','cude'], 'string'],
            [['usuariosistema'], 'string', 'max' => 50],
            [['idcliente'], 'exist', 'skipOnError' => true, 'targetClass' => Cliente::className(), 'targetAttribute' => ['idcliente' => 'idcliente']],
            [['idconceptonota'], 'exist', 'skipOnError' => true, 'targetClass' => Conceptonota::className(), 'targetAttribute' => ['idconceptonota' => 'idconceptonota']],
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
            'idconceptonota' => 'Concepto',
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
    public function getConceptonota()
    {
        return $this->hasOne(Conceptonota::className(), ['idconceptonota' => 'idconceptonota']);
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
