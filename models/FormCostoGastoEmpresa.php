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

    public function rules()
    {
        return [            
           [['fecha_inicio','fecha_corte'], 'required', 'message' => 'Campo requerido'],
            [['observacion'],'string' ,'max' => '100'],
            [['fecha_corte','fecha_inicio'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [   
            'fecha_inicio' => 'Fecha inicio:',
            'fecha_corte' => 'Fecha corte:',
            'observacion' => 'Observacion:',
            
        ];
    }
    
}
