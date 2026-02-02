<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "configuracion_documento_electronico".
 *
 * @property int $id_configuracion
 * @property int $aplica_factura_electronica
 * @property int $aplica_documento_soporte
 * @property int $aplica_nomina_electronica
 * @property string $llave_api_token
 */
class ConfiguracionDocumentoElectronico extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'configuracion_documento_electronico';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_configuracion'], 'required'],
            [['id_configuracion', 'aplica_factura_electronica', 'aplica_documento_soporte', 'aplica_nomina_electronica'], 'integer'],
            [['llave_api_token','llave_uuid'], 'string', 'max' => 2000],
            [['id_configuracion'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_configuracion' => 'Id Configuracion',
            'aplica_factura_electronica' => 'Aplica Factura Electronica',
            'aplica_documento_soporte' => 'Aplica Documento Soporte',
            'aplica_nomina_electronica' => 'Aplica Nomina Electronica',
            'llave_api_token' => 'Llave Api Token',
            'llave_uuid' => 'llave_uuid',
        ];
    }
}
