<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ModeloEditarColilla extends Model
{
    public $deduccion;   
    public $devengado;
    public $codigo_salario;
    public $codigo;


    public function rules()
    {
        return [
            [['deduccion','devengado','codigo_salario','codigo'], 'integer'],
            
        ];
    }

    public function attributeLabels()
    {
        return [
            'deduccion' => 'Valor deduccion:',  
            'devengado' =>'Valor devengado:',
            'codigo_salario' => 'Concepto de salario:',
            'codigo' => 'codigo',
        ];
    }
}
