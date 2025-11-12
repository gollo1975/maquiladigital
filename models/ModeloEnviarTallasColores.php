<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ModeloEnviarTallasColores extends Model
{
    public $tallas;    
    public $colores;
    public $cantidad;

    public function rules()
    {
        return [

           [['tallas', 'colores', 'cantidad'], 'required', 'message' => 'Este campo es obligatorio.'],
            
        ];
    }

    public function attributeLabels()
    {
        return [
            'tallas' => 'Tallas:',   
            'colores' => 'Colores:',
            'cantidad' => 'Cantidad:',
        ];
    }
}
