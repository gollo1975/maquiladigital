<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "documento_soporte_detalle".
 *
 * @property int $id_detalle
 * @property int $id_documento_soporte
 * @property int $id_concepto
 * @property string $descripcion
 * @property int $cantidad
 * @property int $valor_unitario
 * @property int $id_retencion
 * @property int $valor_retencion
 * @property double $porcentaje_retencion
 * @property int $total_pagar
 *
 * @property DocumentoSoporte $documentoSoporte
 * @property ConceptoDocumentoSoporte $concepto
 * @property RetencionFuente $retencion
 */
class DocumentoSoporteDetalle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'documento_soporte_detalle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_documento_soporte', 'id_concepto', 'cantidad', 'valor_unitario', 'id_retencion', 'valor_retencion', 'total_pagar'], 'integer'],
            [['porcentaje_retencion'], 'number'],
            [['descripcion'], 'string', 'max' => 40],
            [['id_documento_soporte'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentoSoporte::className(), 'targetAttribute' => ['id_documento_soporte' => 'id_documento_soporte']],
            [['id_concepto'], 'exist', 'skipOnError' => true, 'targetClass' => ConceptoDocumentoSoporte::className(), 'targetAttribute' => ['id_concepto' => 'id_concepto']],
            [['id_retencion'], 'exist', 'skipOnError' => true, 'targetClass' => RetencionFuente::className(), 'targetAttribute' => ['id_retencion' => 'id_retencion']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_detalle' => 'Id Detalle',
            'id_documento_soporte' => 'Id Documento Soporte',
            'id_concepto' => 'Id Concepto',
            'descripcion' => 'Descripcion',
            'cantidad' => 'Cantidad',
            'valor_unitario' => 'Valor Unitario',
            'id_retencion' => 'Id Retencion',
            'valor_retencion' => 'Valor Retencion',
            'porcentaje_retencion' => 'Porcentaje Retencion',
            'total_pagar' => 'Total Pagar',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentoSoporte()
    {
        return $this->hasOne(DocumentoSoporte::className(), ['id_documento_soporte' => 'id_documento_soporte']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConcepto()
    {
        return $this->hasOne(ConceptoDocumentoSoporte::className(), ['id_concepto' => 'id_concepto']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRetencion()
    {
        return $this->hasOne(RetencionFuente::className(), ['id_retencion' => 'id_retencion']);
    }
}
