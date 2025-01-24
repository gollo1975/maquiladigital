<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "municipio".
 *
 * @property string $idmunicipio
 * @property string $codigomunicipio
 * @property string $municipio
 * @property string $iddepartamento
 * @property int $activo
 *
 * @property Cliente[] $clientes
 * @property Departamento $departamento
 * @property Recibocaja[] $recibocajas
 */
class Municipio extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'municipio';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idmunicipio', 'codigomunicipio', 'municipio','iddepartamento'], 'required', 'message' => 'Campo requerido'],
            [['activo','codigo_api_nomina'], 'integer'],
            [['idmunicipio', 'codigomunicipio', 'iddepartamento'], 'string', 'max' => 15],
            [['municipio'], 'string', 'max' => 100],
            [['idmunicipio'], 'unique'],          
            [['iddepartamento'], 'exist', 'skipOnError' => true, 'targetClass' => Departamento::className(), 'targetAttribute' => ['iddepartamento' => 'iddepartamento']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'idmunicipio' => 'Id',
            'codigomunicipio' => 'Codigo Municipio (Dane)',
            'municipio' => 'Municipio',
            'iddepartamento' => 'Departamento',
            'activo' => 'Activo',
            'codigo_api_nomina' => 'Codigo api nomina',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientes()
    {
        return $this->hasMany(Cliente::className(), ['idmunicipio' => 'idmunicipio']);
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
    public function getRecibocajas()
    {
        return $this->hasMany(Recibocaja::className(), ['idmunicipio' => 'idmunicipio']);
    }
    
    public function getCajaCompensacion()
    {
        return $this->hasMany(CajaCompensacion::className(), ['idmunicipio' => 'idmunicipio']);
    }

    public function getMunicipioCompleto()
    {
        return "{$this->municipio} - {$this->departamento->departamento}";
    }
    
    public function getEstado()
    {
        if ($this->activo == 1){
            $activo = "SI";
        }else{
            $activo = "NO";
        }
        return $activo;
    }
}
