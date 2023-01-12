<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormActualizarSalarioOperario extends Model
{        
   
    public $nuevo_salario;

    public function rules()
    {
        return [            
           [['nuevo_salario'], 'required', 'message' => 'Campo requerido'],
            [['nuevo_salario'], 'integer'],

        ];
    }

    public function attributeLabels()
    {
        return [   
            'nuevo_salario' => 'Nuevo salario:',
           
        ];
    }
    
}
