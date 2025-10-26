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
    public $producto;

    public function rules()
    {
        return [
            [['orden_produccion','buscar','producto'],'integer'],            
           
        ];
    }

    public function attributeLabels()
    {
        return [
            'orden_produccion' => 'Digite la orden de producción:',
            'producto' => 'Linea de producto:',
            'buscar' => 'buscar',                
          
        ];
    }
}
