<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ModeloCambiarIva extends Model
{        
   
    public $nuevo_iva;
    public $tipocomprobante;
    public $color;



    public function rules()
    {
        return [            
            [['nuevo_iva','tipocomprobante'], 'number'],
            [['color'], 'integer'],
          
        ];
    }

    public function attributeLabels()
    {
        return [   
            'nuevo_iva' => 'Nuevo impuesto:',
            'tipocomprobante' => 'Tipo documento soporte:',
            'color' => 'Nuevo color:'
        ];
    }
    
}
