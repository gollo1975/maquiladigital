<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroDocumentoElectronico extends Model
{
    public $documento;
    public $empleado;

    public function rules()
    {
        return [

            [['documento'], 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            [['empleado'],'string'],
           
        ];
    }

    public function attributeLabels()
    {
        return [
            'documento' => 'Documento empleado:',
            'empleado' => 'Nombre empleado:',
        ];
    }
}