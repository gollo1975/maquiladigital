<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ModelCrearColorRemision extends Model
{        
   
    public $color;


    public function rules()
    {
        return [            
           [['color'], 'required', 'message' => 'Campo requerido'],
            [['color'], 'string', 'max' => 30],
        ];
    }

    public function attributeLabels()
    {
        return [   
            'color' => 'Colores:',
            
        ];
    }
    
}
