<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "configuracion_incapacidad".
 *
 * @property int $codigo_incapacidad
 * @property string $nombre
 * @property int $genera_pago
 * @property int $genera_ibc
 * @property int $codigo_salario
 *
 * @property ConceptoSalarios $codigoSalario
 */
class ConfiguracionIncapacidad extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'configuracion_incapacidad';
    }
    
     public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->nombre = strtoupper($this->nombre);        
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre', 'codigo_salario'], 'required'],
            [['genera_pago', 'genera_ibc', 'codigo_salario','codigo'], 'integer'],
            [['porcentaje'], 'number'],
            [['nombre','codigo_api_nomina'], 'string', 'max' => 40],
            [['codigo_salario'], 'exist', 'skipOnError' => true, 'targetClass' => ConceptoSalarios::className(), 'targetAttribute' => ['codigo_salario' => 'codigo_salario']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'codigo_incapacidad' => 'Codigo',
            'nombre' => 'Nombre',
            'genera_pago' => 'Genera Pago',
            'genera_ibc' => 'Genera Ibc',
            'codigo_salario' => 'Concepto Salario',
            'porcentaje' => 'porcentaje',
            'codigo' => 'Tipo',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConceptoSalarios()
    {
        return $this->hasOne(ConceptoSalarios::className(), ['codigo_salario' => 'codigo_salario']);
    }
     public function getConfiguracionIncapacidad()
    {
        return $this->hasMany(ConfiguracionIncapacidad::className(), ['codigo_incapacidad' => 'codigo_incapacidad']);
    }
    public function getgeneraPago()
    {
        if($this->genera_pago == 1){
            $generapago= "SI";
        }else{
            $generapago = "NO";
        }
        return $generapago;
    }
    public function getgeneraIbc()
    {
        if($this->genera_ibc == 1){
            $generaibc= "SI";
        }else{
            $generaibc = "NO";
        }
        return $generaibc;
    }
    public function getTipoIncapacidad()
    {
        if($this->codigo == 1){
            $tipoincapacidad= "INCAPACIDAD GENERAL";
        }else{
            $tipoincapacidad = "INCAPACIDAD LABORAL";
        }
        return $tipoincapacidad;
    }
}
