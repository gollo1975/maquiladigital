<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroProductos extends Model
{
    public $idcliente;
    public $referencia;
   
        
    public function rules()
    {
        return [

            [['idcliente'], 'integer'],
            ['referencia', 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [

            'idcliente' => 'Cliente:',
            'referencia' => 'Referencia:',
          
        ];
    }
}
