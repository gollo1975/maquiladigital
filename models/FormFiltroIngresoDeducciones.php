<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroIngresoDeducciones extends Model
{
    public $fecha_inicio;
    public $fecha_corte;


    public function rules()
    {
        return [

            [['fecha_inicio', 'fecha_corte'], 'safe'],
           
           
        ];
    }

    public function attributeLabels()
    {
        return [
            'fecha_inicio' =>'Fecha de iniicio:',
            'fecha_corte' => 'Fecha de corte:',
           
           
        ];
    }
}