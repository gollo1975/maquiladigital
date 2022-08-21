<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "configuracion_salario".
 *
 * @property int $id_salario
 * @property int $salario_minino_actual
 * @property int $salario_minimo_anterior
 * @property int $auxilio_transporte_actual
 * @property int $auxilio_transporte_anterior
 * @property int $anio
 * @property int $estado
 * @property string $usuario
 * @property string $fecha_creacion
 */
class ConfiguracionSalario extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'configuracion_salario';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['salario_minimo_actual', 'salario_minimo_anterior', 'auxilio_transporte_actual', 'auxilio_transporte_anterior', 'anio', 'estado','salario_incapacidad'], 'integer'],
            [['porcentaje_incremento'], 'number'],
            [['salario_minimo_actual','auxilio_transporte_actual','anio','fecha_cierre','fecha_aplicacion'], 'required', 'message' => 'Este campo no puede ser vacio'],
            [['fecha_creacion'], 'safe'],
            [['usuario'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_salario' => 'Id',
            'salario_minimo_actual' => 'Salario minino actual',
            'salario_minimo_anterior' => 'Salario minimo anterior',
            'auxilio_transporte_actual' => 'Auxilio transporte actual',
            'auxilio_transporte_anterior' => 'Auxilio transporte anterior',
            'salario_incapacidad' => 'Salario incapacidad',
            'porcentaje_incremento' => 'Porcentaje incremento',
            'anio' => 'Año',
            'estado' => 'Activo',
            'usuario' => 'Usuario',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_cierre' => 'Fecha cierre:',
            'fecha_aplicacion' => 'Fecha aplicacion:',
        ];
    }
    public function getActivo()
    {
        if ($this->estado == 1){
            $estado = 'SI';
    }else {
           $estado = 'NO'; 
        } 
        return $estado; 
    }
}
