<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormCambioHoraModulo extends Model
{        
   
    public $hora_inicio;
    
    public function rules()
    {
        return [            
           [['hora_inicio'], 'required', 'message' => 'Campo requerido'],
       
        ];
    }

    public function attributeLabels()
    {
        return [   
            'hora_inicio' => 'Hora inicio:',
            
        ];
    }
    
}
