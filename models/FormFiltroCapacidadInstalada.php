<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroCapacidadInstalada extends Model
{
    public $horario;
    public $tipo_servicio;

    public function rules()
    {
        return [

            [['horario','tipo_servicio'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'horario' => 'Horario de trabajo:',
            'tipo_servicio' => 'Tipo de servicio:',
           
        ];
    }
}
