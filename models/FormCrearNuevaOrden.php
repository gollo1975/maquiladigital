<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormCrearNuevaOrden extends Model
{        
   
    public $tipo_servicio;
    public $observacion;

    public function rules()
    {
        return [            
          
            [['tipo_servicio'], 'integer'],
            [['observacion'],'string' ,'max' => '100'],
        ];
    }

    public function attributeLabels()
    {
        return [   
            'observacion' => 'Observacion:',
            'tipo_servicio' => 'Tipo de orden:',
            
        ];
    }
    
}
