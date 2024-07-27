<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroReferencia extends Model
{
    public $codigo;
    public $referencia;
    public $tipo_prenda;
   
    public function rules()
    {
        return [

            [['codigo', 'tipo_prenda'], 'integer'],
            [['referencia'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'codigo' => 'Tipo pago:',
            'tipo_prenda' => 'Grupo:',
            'referencia' =>'Referencia:',
        ];
    }
}