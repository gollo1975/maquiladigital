<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormImportarOperaciones extends Model
{
    public $orden_produccion;  
    public $buscar;

    public function rules()
    {
        return [
            [['orden_produccion'],'required'],     //, 'messaje' => 'Campo requerido para la consulta'    
            [['orden_produccion','buscar'],'integer'],            
           
        ];
    }

    public function attributeLabels()
    {
        return [
            'orden_produccion' => 'Digite la orden de producción/Salida de bodega:',
            'buscar' => 'buscar',                
          
        ];
    }
}
