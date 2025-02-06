<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormCostoGastoEmpresa extends Model
{        
   
    public $fecha_inicio;
    public $fecha_corte;
    public  $observacion;
    public $hora_inicio;
    public $hora_corte;
    public $tipo_nomina;
    public $fecha_proceso;
    public $codigo;

    public function rules()
    {
        return [            
           [['fecha_inicio','fecha_corte'], 'required', 'message' => 'Campo requerido'],
           [['observacion'],'string' ,'max' => '100'],
           [['fecha_corte','fecha_inicio','fecha_proceso'], 'safe'],
           [['hora_inicio', 'hora_corte'], 'string'],
           [['tipo_nomina','codigo'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [   
            'fecha_inicio' => 'Fecha inicio:',
            'fecha_corte' => 'Fecha corte:',
            'observacion' => 'Observacion:',
            'hora_inicio' => 'Hora de inicio:',
            'hora_corte' => 'Hora de corte:',
            'tipo_nomina' => 'Tipo de nomina:',
            'fecha_proceso' => 'Fecha cambio:',
            'codigo' => 'Codigo:',
            
        ];
    }
    
}
