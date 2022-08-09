<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mecanico".
 *
 * @property int $id_mecanico
 * @property int $id_tipo_documento
 * @property string $documento
 * @property string $nombres
 * @property string $apellidos
 * @property string $email_mecanico
 * @property string $celular
 * @property string $direccion_mecanico
 * @property string $iddepartamento
 * @property string $idmunicipio
 * @property int $estado
 * @property string $usuario
 * @property string $observacion
 *
 * @property MantenimientoMaquina[] $mantenimientoMaquinas
 * @property Tipodocumento $tipoDocumento
 */
class Mecanico extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mecanico';
    }
    
    public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->nombres = strtolower($this->nombres); 
        $this->apellidos = strtoupper($this->apellidos);
        $this->direccion_mecanico = strtoupper($this->direccion_mecanico);
        $this->email_mecanico = strtolower($this->email_mecanico);
        $this->observacion = strtolower($this->observacion);
        $this->observacion = ucfirst($this->observacion);  
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_tipo_documento', 'documento', 'nombres', 'apellidos', 'iddepartamento', 'idmunicipio','estado'], 'required'],
            [['id_tipo_documento', 'estado'], 'integer'],
            [['documento', 'celular', 'iddepartamento', 'idmunicipio', 'usuario'], 'string', 'max' => 15],
            [['nombres', 'apellidos'], 'string', 'max' => 30],
            [['email_mecanico'], 'string', 'max' => 45],
            [['direccion_mecanico', 'observacion'], 'string', 'max' => 50],
            [['fecha_registro'], 'safe'],
            [['id_tipo_documento'], 'exist', 'skipOnError' => true, 'targetClass' => Tipodocumento::className(), 'targetAttribute' => ['id_tipo_documento' => 'id_tipo_documento']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_mecanico' => 'Id',
            'id_tipo_documento' => 'Tipo documento:',
            'documento' => 'Documento:',
            'nombres' => 'Nombres:',
            'apellidos' => 'Apellidos:',
            'email_mecanico' => 'Email:',
            'celular' => 'Celular:',
            'direccion_mecanico' => 'Dirección:',
            'iddepartamento' => 'Departamento:',
            'idmunicipio' => 'Municipio:',
            'estado' => 'Activo:',
            'usuario' => 'Usuario:',
            'observacion' => 'Observacion:',
            'fecha_registro' => 'Fecha Registro:',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMantenimientoMaquinas()
    {
        return $this->hasMany(MantenimientoMaquina::className(), ['id_mecanico' => 'id_mecanico']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoDocumento()
    {
        return $this->hasOne(Tipodocumento::className(), ['id_tipo_documento' => 'id_tipo_documento']);
    }
    
    public function getDepartamentoMecanico()
    {
        return $this->hasOne(Departamento::className(), ['iddepartamento' => 'iddepartamento']);
    }
    
     public function getMunicipio()
    {
        return $this->hasOne(Municipio::className(), ['idmunicipio' => 'idmunicipio']);
    }
    
    public function getActivo()
    {
        if ($this->estado=='0'){
            $activo = 'SI';
        }else{
            $activo = 'NO';
        }
        return $activo;
    }
}
