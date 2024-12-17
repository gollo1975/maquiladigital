<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "documento_electronico".
 *
 * @property int $id_documento
 * @property string $nombre_documento
 * @property string $consecutivo
 * @property int $codigo_interface
 */
class DocumentoElectronico extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'documento_electronico';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre_documento', 'consecutivo', 'codigo_interface'], 'required'],
            [['codigo_interface'], 'integer'],
            [['nombre_documento'], 'string', 'max' => 40],
            [['consecutivo'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_documento' => 'Id Documento',
            'nombre_documento' => 'Nombre Documento',
            'consecutivo' => 'Consecutivo',
            'codigo_interface' => 'Codigo Interface',
        ];
    }
}
