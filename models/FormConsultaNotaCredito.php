<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormConsultaNotaCredito extends Model
{
    public $nueva_cantidad;
    public $valor_unitario;


    public function rules()
    {
        return [

            ['nueva_cantidad', 'integer'],
            ['valor_unitario','number'],
            [['nueva_cantidad','valor_unitario'], 'required', 'message' => 'Campo requerido'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'valor_unitario' => 'Precio unitario:',
            'nueva_cantidad' => 'Cantidad:',
        ];
    }
}