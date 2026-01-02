<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ModelSimuladorTiempo extends Model
{
    public $cantidad_operarios;
    public $horario_trabajo;
    public $eficiencia;
    public $vlr_minuto_contrato;
    public $vinculado;
    public $unidades;
    public $salario;
    public $tiempo_confeccion;
    public $id_cliente;
    public $id_simulador;
    public $fecha_inicio;
    public $fecha_final;
    public $dias_proceso;
    
    public function rules()
    {
        return [            
            [['cantidad_operarios','horario_trabajo','unidades','tiempo_confeccion','id_cliente'],'required', 'message' => 'Campo requerido para generar el simulador'],
            [['cantidad_operarios','horario_trabajo','unidades'], 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            [['cantidad_operarios','horario_trabajo','unidades','vinculado','unidades','salario',
                'vlr_minuto_contrato','id_cliente'],'integer'],
            [['dias_proceso', 'tiempo_confeccion','eficiencia'], 'number'],
            [['id_cliente','id_simulador'], 'default'],
            [['fecha_inicio','fecha_final'], 'safe'],
            
        ];
    }

    public function attributeLabels()
    {
        return [
            'cantidad_operarios' => 'Nro Operarias:',
            'horario_trabajo' => 'Horario de trabajo:',
            'unidades' => 'Unidades:',
            'tiempo_confeccion' => 'Sam:',
            'vinculado' => 'Vinculado:',
            'eficiencia' => '% Eficiencia:',
            'salario' => 'Salario:',
            'vlr_minuto_contrato' => 'Vr. minuto contrato:',
            'id_cliente' => 'Cliente:',
            'id_simulador' => 'id_simulador',
            'fecha_inicio' => 'Fecha inicio:',
            
        ];
    }
}
