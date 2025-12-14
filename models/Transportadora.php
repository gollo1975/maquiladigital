<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "transportadora".
 *
 * @property int $id_transportadora
 * @property int $id_tipo_documento
 * @property string $nit_cedula
 * @property int $dv
 * @property string $razon_social
 * @property string $direccion
 * @property string $email_transportadora
 * @property string $telefono
 * @property string $celular
 * @property string $iddepartamento
 * @property string $idmunicipio
 * @property string $contacto
 * @property string $celular_contacto
 * @property string $user_name
 * @property string $fecha_registro
 *
 * @property TipoDocumento $tipoDocumento
 */
class Transportadora extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transportadora';
    }
    
     public function beforeSave($insert) {
	if(!parent::beforeSave($insert)){
            return false;
        }
	# ToDo: Cambiar a cliente cargada de configuración.    
	$this->razon_social = strtoupper($this->razon_social);
	$this->direccion = strtoupper($this->direccion);
        $this->email_transportadora = strtolower($this->email_transportadora);
        $this->contacto = strtoupper($this->contacto);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_tipo_documento', 'cedulanit', 'razon_social', 'direccion', 'email_transportadora', 'celular', 'iddepartamento', 'idmunicipio'], 'required'],
            [['id_tipo_documento',], 'integer'],
            [['fecha_registro'], 'safe'],
            [['cedulanit', 'telefono', 'celular', 'iddepartamento', 'idmunicipio', 'celular_contacto', 'user_name'], 'string', 'max' => 15],
            [['razon_social', 'direccion', 'email_transportadora'], 'string', 'max' => 50],
            [['contacto'], 'string', 'max' => 40],
            [['id_tipo_documento'], 'exist', 'skipOnError' => true, 'targetClass' => TipoDocumento::className(), 'targetAttribute' => ['id_tipo_documento' => 'id_tipo_documento']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_transportadora' => 'Codigo',
            'id_tipo_documento' => 'Tipo de documento:',
            'cedulanit' => 'Documento:',
            'razon_social' => 'Razon social:',
            'direccion' => 'Direccion:',
            'email_transportadora' => 'Email:',
            'telefono' => 'Telefono:',
            'celular' => 'Celular:',
            'iddepartamento' => 'Departamento:',
            'idmunicipio' => 'Municipio:',
            'contacto' => 'Contacto:',
            'celular_contacto' => 'Celular:',
            'user_name' => 'User Name',
            'fecha_registro' => 'Fecha Registro',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoDocumento()
    {
        return $this->hasOne(Tipodocumento::className(), ['id_tipo_documento' => 'id_tipo_documento']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartamento()
    {
        return $this->hasOne(Departamento::className(), ['iddepartamento' => 'iddepartamento']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMunicipio()
    {
        return $this->hasOne(Municipio::className(), ['idmunicipio' => 'idmunicipio']);
    }
}
