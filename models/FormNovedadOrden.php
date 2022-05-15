<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormNovedadOrden extends Model
{        
   
    
    public  $idordenproduccion;
    public  $novedad;
    public $autorizado;
    


    public function rules()
    {
        return [            
           [['idordenproduccion','novedad','autorizado'], 'required', 'message' => 'Campo requerido'],
            [['idordenproduccion','autorizado'], 'integer'],
            [['novedad'], 'string'],
         
        ];
    }

    public function attributeLabels()
    {
        return [   
            'novedad' => 'Novedad:',
            'idordenproduccion' => 'Orden produccion:',
          
        ];
    }
    
}
