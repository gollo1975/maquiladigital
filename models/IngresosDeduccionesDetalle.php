<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ingresos_deducciones_detalle".
 *
 * @property int $id_detalle
 * @property int $id_ingreso
 * @property int $codigo_salario
 * @property int $suma_resta
 * @property int $id_empleado
 * @property int $valor_pagado
 * @property string $observacion
 *
 * @property IngresosDeducciones $ingreso
 * @property ConceptoSalarios $codigoSalario
 * @property Empleado $empleado
 */
class IngresosDeduccionesDetalle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ingresos_deducciones_detalle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_ingreso', 'codigo_salario', 'suma_resta', 'id_empleado', 'valor_pagado','importado'], 'integer'],
            [['codigo_salario', 'id_empleado'], 'required'],
            [['observacion'], 'string', 'max' => 100],
            [['fecha_inicio','fecha_corte'], 'safe'],
            [['id_ingreso'], 'exist', 'skipOnError' => true, 'targetClass' => IngresosDeducciones::className(), 'targetAttribute' => ['id_ingreso' => 'id_ingreso']],
            [['codigo_salario'], 'exist', 'skipOnError' => true, 'targetClass' => ConceptoSalarios::className(), 'targetAttribute' => ['codigo_salario' => 'codigo_salario']],
            [['id_empleado'], 'exist', 'skipOnError' => true, 'targetClass' => Empleado::className(), 'targetAttribute' => ['id_empleado' => 'id_empleado']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_detalle' => 'Id',
            'id_ingreso' => 'Codigo',
            'codigo_salario' => 'Concepto de salario',
            'suma_resta' => 'Suma/Resta:',
            'id_empleado' => 'Nombre del empleado:',
            'valor_pagado' => 'Valor pagado:',
            'observacion' => 'Observacion:',
            'fecha_inicio' => 'Fecha inicio:',
            'fecha_corte' => 'Fecha corte:',
            'importado' => 'importado',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIngreso()
    {
        return $this->hasOne(IngresosDeducciones::className(), ['id_ingreso' => 'id_ingreso']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCodigoSalario()
    {
        return $this->hasOne(ConceptoSalarios::className(), ['codigo_salario' => 'codigo_salario']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmpleado()
    {
        return $this->hasOne(Empleado::className(), ['id_empleado' => 'id_empleado']);
    }
    
     public function getSumaResta() {
        if ($this->suma_resta == 0){
            $sumaresta = 'TODAS';
        }else{
            if ($this->suma_resta == 1){
                $sumaresta = 'SUMA';
            }else{
               $sumaresta = 'RESTA';
            }   
        }
        return $sumaresta;
        
    }
    
    

}
