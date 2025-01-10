<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroBuscarNomina extends Model
{
    public $desde;
    public $hasta;
    
    public function rules()
    {
        return [

            [['desde', 'hasta'], 'safe'],
           
        ];
    }

    public function attributeLabels()
    {
        return [
            'desde' => 'Fecha_inicio',
            'hasta' => 'Fecha corte:',
           
        ];
    }
}