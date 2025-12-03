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
    public $cantidad_cajas;
    public $cantidad_despachada;



    public function rules()
    {
        return [            
            [['nuevo_iva','tipocomprobante'], 'number'],
            [['color','cantidad_despachada','cantidad_cajas'], 'integer'],
          
        ];
    }

    public function attributeLabels()
    {
        return [   
            'nuevo_iva' => 'Nuevo impuesto:',
            'tipocomprobante' => 'Tipo documento soporte:',
            'color' => 'Nuevo color:',
            'cantidad_cajas' => 'Numero de cajas:',
            'cantidad_despachada' => 'Unidades a despachar:'
            
        ];
    }
    
}
