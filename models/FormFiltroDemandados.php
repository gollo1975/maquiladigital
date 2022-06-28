<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroCliente extends Model
{
    public $cedulanit;
    public $nombrecorto;

    public function rules()
    {
        return [

            ['cedulanit', 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            ['nombrecorto', 'match', 'pattern' => '/^[a-z\s]+$/i', 'message' => 'Sólo se aceptan letras'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'cedulanit' => 'Nro Identificacion',
            'nombrecorto' => 'Cliente:',
        ];
    }
}
