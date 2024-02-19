<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ModelAsignacionTalla extends Model
{        
   
    public $planta;
    
    public function rules()
    {
        return [            
            [['planta'], 'integer'],
           [['planta'], 'required', 'message' => 'Campo requerido'],
       
        ];
    }

    public function attributeLabels()
    {
        return [   
            'planta' => 'Nombre planta:',
            
        ];
    }
    
}
