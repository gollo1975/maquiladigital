<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroMecanico extends Model
{
    public $documento;
    public $nombre_completo;

    public function rules()
    {
        return [

            ['documento', 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            ['nombre_completo', 'match', 'pattern' => '/^[a-z\s]+$/i', 'message' => 'Sólo se aceptan letras'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'documento' => 'Documento:',
            'nombre_completo' => 'Mecanico:',
        ];
    }
}
