<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "documento_soporte".
 *
 * @property int $id_documento_soporte
 * @property int $idproveedor
 * @property int $id_compra
 * @property string $documento_compra
 * @property string $fecha_elaboracion
 * @property string $fecha_hora_registro
 * @property string $fecha_recepcion_dian
 * @property string $fecha_envio_api
 * @property int $numero_soporte
 * @property string $cuds
 * @property string $qrstr
 * @property int $id_forma_pago
 * @property int $autorizado
 * @property string $user_name
 *
 * @property Proveedor $proveedor
 * @property Compra $compra
 * @property FormaPago $formaPago
 * @property DocumentoSoporteDetalle[] $documentoSoporteDetalles
 */
class DocumentoSoporte extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'documento_soporte';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idproveedor', 'id_compra', 'numero_soporte', 'id_forma_pago', 'autorizado','valor_pagar'], 'integer'],
            [['fecha_elaboracion'], 'required'],
            [['fecha_elaboracion', 'fecha_hora_registro', 'fecha_recepcion_dian', 'fecha_envio_api'], 'safe'],
            [['documento_compra', 'user_name'], 'string', 'max' => 15],
            [['cuds'], 'string', 'max' => 350],
            [['qrstr'], 'string', 'max' => 2000],
            ['observacion', 'string', 'max' => 50],
            [['idproveedor'], 'exist', 'skipOnError' => true, 'targetClass' => Proveedor::className(), 'targetAttribute' => ['idproveedor' => 'idproveedor']],
            [['id_compra'], 'exist', 'skipOnError' => true, 'targetClass' => Compra::className(), 'targetAttribute' => ['id_compra' => 'id_compra']],
            [['id_forma_pago'], 'exist', 'skipOnError' => true, 'targetClass' => FormaPago::className(), 'targetAttribute' => ['id_forma_pago' => 'id_forma_pago']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_documento_soporte' => 'Id:',
            'idproveedor' => 'Proveedor:',
            'id_compra' => 'Factura/Cuenta cobro:',
            'documento_compra' => 'Documento de compra:',
            'fecha_elaboracion' => 'Fecha elaboracion:',
            'fecha_hora_registro' => 'Fecha Hora registro:',
            'fecha_recepcion_dian' => 'Fecha recepcion dian:',
            'fecha_envio_api' => 'Fecha envio api:',
            'numero_soporte' => 'Numero soporte:',
            'cuds' => 'Cuds',
            'qrstr' => 'Qrstr',
            'id_forma_pago' => 'Forma de pago',
            'autorizado' => 'Autorizado',
            'user_name' => 'User Name',
            'valor_pagar' => 'valor_pagar',
            'observacion' => 'observacion',
        ];
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
    public function getCompra()
    {
        return $this->hasOne(Compra::className(), ['id_compra' => 'id_compra']);
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
    public function getDocumentoSoporteDetalles()
    {
        return $this->hasMany(DocumentoSoporteDetalle::className(), ['id_documento_soporte' => 'id_documento_soporte']);
    }
}
