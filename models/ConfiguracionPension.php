<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "configuracion_pension".
 *
 * @property int $id_grupo_pago
 * @property string $grupo_pago
 * @property int $estado
 * @property string $nombre_periodo
 * @property string $iddepartamento
 * @property string $idmunicipio
 * @property int $id_sucursal
 */
class ConfiguracionPension extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'configuracion_pension';
    }
    
    public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->concepto = strtoupper($this->concepto);        
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['codigo_salario', 'concepto','porcentaje_empleado', 'porcentaje_empleador'], 'required'],
            [['codigo_salario'], 'integer'],
            [['porcentaje_empleado', 'porcentaje_empleador'], 'number'],
            [['concepto','codigo_api_nomina'], 'string', 'max' => 30],
            ['concepto', 'match', 'pattern' => '/^[a-z\s]+$/i', 'message' => 'Sólo se aceptan letras'],
           
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_pension' => 'Id',
            'codigo_salario' => 'Concepto salario',
            'concepto' => 'Concepto',
            'porcentaje_empleado' => '% Empleado',
            'porcentaje_empleador' => '% Empleador',
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