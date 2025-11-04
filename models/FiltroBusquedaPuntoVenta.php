<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FiltroBusquedaPuntoVenta extends Model
{        
   
    public $nombre_punto;
  
       
    
    public function rules()
    {
        return [  
          
           [['nombre_punto'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [   
            'nombre_punto' => 'Nombre del punto:',
         

        ];
    }
    
}
