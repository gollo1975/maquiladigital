<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FiltroBusquedaTallas extends Model
{        
   
    public $codigo_talla;
   
    public function rules()
    {
        return [  
           [['codigo_talla'], 'integer'],
          
        ];
    }

    public function attributeLabels()
    {
        return [   
            'codigo_talla' => 'Tallas:',
                      
       
        ];
    }
    
}