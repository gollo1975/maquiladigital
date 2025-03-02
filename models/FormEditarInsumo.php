<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormEditarInsumo extends Model
{        
   
    public $cantidad;
    public $numero;
    public $convertir;
    
    public function rules()
    {
        return [            
           [['cantidad','numero','convertir'], 'integer'],
           [['numero'], 'number'],
       
        ];
    }

    public function attributeLabels()
    {
        return [   
            'cantidad' => 'Cantidad:',
            'numero' => 'Metros / unidades:',
            'convertir' => 'Convertir:',
            
        ];
    }
    
}
