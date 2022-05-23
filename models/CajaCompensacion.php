<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "caja_compensacion".
 *
 * @property int $id_caja_compensacion
 * @property string $caja
 * @property int $estado
 * @property int $telefono
 * @property int $direccion
 * @property int $codigo_caja
 * @property int $codigo_interfaz
 * @property int $idmunicipio
 *
 * @property Contrato[] $contratos
 */
class CajaCompensacion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'caja_compensacion';
    }

    public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->caja = strtoupper($this->caja);        
        return true;
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['caja','idmunicipio','porcentaje_caja'], 'required'],
            [['estado'], 'integer'],
            [['porcentaje_caja'], 'number'],
            [['telefono'], 'string', 'max' => 15],
            [['direccion'], 'string', 'max' => 100],
            [['codigo_caja'], 'string', 'max' => 20],
            [['codigo_interfaz'], 'string', 'max' => 20],
            [['idmunicipio'], 'string', 'max' => 15],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_caja_compensacion' => 'Id',
            'caja' => 'Caja',
            'telefono' => 'Telefono',
            'direccion' => 'Direccion',
            'codigo_caja' => 'Cod Caja',
            'codigo_interfaz' => 'Cod Interfaz',
            'idmunicipio' => 'Municipio',
            'estado' => 'Estado',
            'porcentaje_caja' => '% Caja:',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContratos()
    {
        return $this->hasMany(Contrato::className(), ['id_caja_compensacion' => 'id_caja_compensacion']);
    }
    
    public function getCajaCompensacion()
    {
        return $this->hasOne(CajaCompensacion::className(), ['idmunicipio' => 'idmunicipio']);
    }
    
    public function getActivo()
    {
        if($this->estado == 1){
            $estado = "SI";
        }else{
            $estado = "NO";
        }
        return $estado;
    }
    
    public function getMunicipios()
    {        
        $municipio = Municipio::findOne($this->idmunicipio);
        return $municipio->municipio;
    }
}
