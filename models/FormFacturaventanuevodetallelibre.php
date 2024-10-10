<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormFacturaventanuevodetallelibre extends Model
{    
    public $valor;
    public $idproducto;
    public $cantidad;
    public function rules()
    {
        return [            
            [['valor','idproducto','cantidad'], 'required'],            
            [['idproducto','valor','cantidad'], 'integer'],                       
        ];
    }

    public function attributeLabels()
    {
        return [                        
            'valor' => 'Valor:',   
            'idproducto' => 'Servicio / Producto:', 
            'cantidad' => 'Cantidad:',
        ];
    }
    
}
