<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "departamento".
 *
 * @property string $iddepartamento
 * @property string $departamento
 * @property int $activo
 *
 * @property Cliente[] $clientes
 * @property Municipio[] $municipios
 */
class Departamento extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'departamento';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['iddepartamento', 'departamento','id_pais'], 'required', 'message' => 'Campo requerido'],
            [['activo','id_pais'], 'integer'],
            [['iddepartamento','codigo_api_nomina'], 'string', 'max' => 15],
            [['departamento'], 'string', 'max' => 100],
            [['iddepartamento'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'iddepartamento' => 'Id (Dane)',
            'departamento' => 'Departamento',
            'activo' => 'Activo',
            'id_pais' => 'Pais:',
            'codigo_api_nomina' => 'Codigo api nomina',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientes()
    {
        return $this->hasMany(Cliente::className(), ['iddepartamento' => 'iddepartamento']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMunicipios()
    {
        return $this->hasMany(Municipio::className(), ['iddepartamento' => 'iddepartamento']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPais()
    {
        return $this->hasOne(Paises::className(), ['id_pais' => 'id_pais']);
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
