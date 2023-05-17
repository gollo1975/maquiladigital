<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "comprobante_egreso".
 *
 * @property int $id_comprobante_egreso
 * @property string $id_municipio
 * @property string $fecha
 * @property string $fecha_comprobante
 * @property int $numero
 * @property int $id_comprobante_egreso_tipo
 * @property double $valor
 * @property int $id_proveedor
 * @property string $observacion
 * @property string $usuariosistema
 * @property int $estado
 * @property int $autorizado
 * @property int $libre
 * @property int $id_banco
 * @property double $subtotal
 * @property double $iva
 * @property double $retefuente
 * @property double $reteiva
 * @property double $reteica
 * @property double $base_aiu
 *
 * @property Municipio $municipio
 * @property ComprobanteEgresoTipo $comprobanteEgresoTipo
 * @property Proveedor $proveedor
 * @property Banco $banco
 * @property ComprobanteEgresoDetalle[] $comprobanteEgresoDetalles
 */
class ComprobanteEgreso extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'comprobante_egreso';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_municipio', 'fecha_comprobante', 'id_comprobante_egreso_tipo', 'id_proveedor', 'id_banco'], 'required'],
            [['fecha', 'fecha_comprobante'], 'safe'],
            [['numero', 'id_comprobante_egreso_tipo', 'id_proveedor', 'estado', 'autorizado', 'libre', 'id_banco'], 'integer'],
            [['valor','subtotal','iva','retefuente','reteiva','reteica','base_aiu'], 'number'],
            [['observacion'], 'string'],
            [['id_municipio'], 'string', 'max' => 15],
            [['usuariosistema'], 'string', 'max' => 30],
            [['id_municipio'], 'exist', 'skipOnError' => true, 'targetClass' => Municipio::className(), 'targetAttribute' => ['id_municipio' => 'idmunicipio']],
            [['id_comprobante_egreso_tipo'], 'exist', 'skipOnError' => true, 'targetClass' => ComprobanteEgresoTipo::className(), 'targetAttribute' => ['id_comprobante_egreso_tipo' => 'id_comprobante_egreso_tipo']],
            [['id_proveedor'], 'exist', 'skipOnError' => true, 'targetClass' => Proveedor::className(), 'targetAttribute' => ['id_proveedor' => 'idproveedor']],
            [['id_banco'], 'exist', 'skipOnError' => true, 'targetClass' => Banco::className(), 'targetAttribute' => ['id_banco' => 'idbanco']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_comprobante_egreso' => 'Id',
            'id_municipio' => 'Municipio',
            'fecha' => 'Fecha',
            'fecha_comprobante' => 'Fecha Comprobante',
            'numero' => 'Numero',
            'id_comprobante_egreso_tipo' => 'Tipo',
            'valor' => 'Valor',
            'id_proveedor' => 'Proveedor',
            'observacion' => 'Observacion',
            'usuariosistema' => 'Usuariosistema',
            'estado' => 'Estado',
            'autorizado' => 'Autorizado',
            'libre' => 'Libre',
            'id_banco' => 'Banco',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMunicipio()
    {
        return $this->hasOne(Municipio::className(), ['idmunicipio' => 'id_municipio']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComprobanteEgresoTipo()
    {
        return $this->hasOne(ComprobanteEgresoTipo::className(), ['id_comprobante_egreso_tipo' => 'id_comprobante_egreso_tipo']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProveedor()
    {
        return $this->hasOne(Proveedor::className(), ['idproveedor' => 'id_proveedor']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBanco()
    {
        return $this->hasOne(Banco::className(), ['idbanco' => 'id_banco']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComprobanteEgresoDetalles()
    {
        return $this->hasMany(ComprobanteEgresoDetalle::className(), ['id_comprobante_egreso' => 'id_comprobante_egreso']);
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
