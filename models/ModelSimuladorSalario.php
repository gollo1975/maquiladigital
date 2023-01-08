<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ModelSimuladorSalario extends Model
{
    public $salario_basico;
    public $arl;
    public $aplica_auxilio;
    public $eficiencia;
    public $valor_minuto;
    public $sam;
    public $dias_laborados;
    public $id_horario;
    public $otros_gastos;


    public function rules()
    {
        return [            
            [['salario_basico','arl'],'required', 'message' => 'Campo requerido para generar el simulador de salarios'],
            [['salario_basico','arl'], 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            [['salario_basico','arl','aplica_auxilio','valor_minuto','dias_laborados','id_horario','otros_gastos'], 'integer'],
            [['eficiencia','sam'], 'number'],
            
        ];
    }

    public function attributeLabels()
    {
        return [
            'arl' => 'Porcentaje arl:',
            'salario_basico' => 'Salario propuesto:',
            'aplica_auxilio' => 'aplica_auxilio',
            'dias_laborados' => 'Dias laborados:',
            'sam' => 'Tiempo prenda:',
            'valor_minuto' => 'Minuto venta:',
            'eficiencia' => '% Eficiencia:',
            'id_horario' => 'Horario:',
            'otros_gastos' => 'Otros gastos:',
            
        ];
    }
}
