<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ModeloEntradaProducto extends Model
{        
   
    public $codigo_producto;
    public function rules()
    {
        return [  
           [['codigo_producto'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [   
            'codigo_producto' => 'Codigo del producto:',

          
       
        ];
    }
    
}