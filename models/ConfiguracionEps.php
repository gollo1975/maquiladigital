<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "configuracion_eps".
 *
 * @property int $id_eps
 * @property int $codigo_salario
 * @property string $concepto_eps
 */
class ConfiguracionEps extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'configuracion_eps';
    }
    
    public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->concepto_eps = strtoupper($this->concepto_eps);        
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['codigo_salario', 'concepto_eps','porcentaje_empleado_eps', 'porcentaje_empleador_eps'], 'required'],
            [['codigo_salario'], 'integer'],
            [['porcentaje_empleado_eps', 'porcentaje_empleador_eps'], 'number'],
            [['concepto_eps','codigo_api_nomina'], 'string', 'max' => 30],
            ['concepto_eps', 'match', 'pattern' => '/^[a-zA-Z]+$/i', 'message' => 'Sólo se aceptan letras'],
           
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_eps' => 'Id',
            'codigo_salario' => 'Concepto salario',
            'concepto_eps' => 'Concepto',
            'porcentaje_empleado_eps' => '% Empleado_eps',
            'porcentaje_empleador_eps' => '% Empleador_eps',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    
     public function getConceptoSalarios()
    {
        return $this->hasOne(ConceptoSalarios::className(), ['codigo_salario' => 'codigo_salario']);
    }
    
   
 /*   public function getActivo()
    {
        if($this->estado == 1){
            $activo = "SI";
        }else{
            $activo = "NO";
        }
        return $activo;
    }*/
}